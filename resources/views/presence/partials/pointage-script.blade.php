{{--
    Capture GPS et empreinte d'appareil pour le pointage classique.
    Même contrat de champs que le scan QR : l'écran de validation intermédiaire
    ayant disparu, tout part en une seule soumission.
--}}
@once
@push('scripts')
<script>
    function pointageForm(enRetard = false) {
        return {
            busy: false,
            etape: '',
            erreur: '',

            // Le motif de retard se saisit en modale : la page reste lisible et
            // le champ n'apparaît qu'au moment où il conditionne l'enregistrement.
            enRetard: enRetard,
            modalRetard: false,
            motif: '',
            motifTropCourt: false,

            validerMotif() {
                if (this.motif.trim().length < 10) {
                    this.motifTropCourt = true;
                    return;
                }
                this.motifTropCourt = false;
                this.modalRetard = false;
                // x-data est porté par le <form> : $root est ce formulaire.
                this.capturer(this.$root);
            },

            deviceUuid() {
                let uuid = localStorage.getItem('tfg_device_uuid');
                if (!uuid) {
                    uuid = 'dev_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36);
                    localStorage.setItem('tfg_device_uuid', uuid);
                }
                return uuid;
            },

            fingerprint() {
                const ecran = `${screen.width}x${screen.height}x${screen.colorDepth}`;
                const zone  = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
                const nav   = `${navigator.userAgent}|${navigator.language}|${navigator.hardwareConcurrency || 2}`;
                const brut  = `${this.deviceUuid()}|${ecran}|${zone}|${nav}`;

                let h = 0;
                for (let i = 0; i < brut.length; i++) {
                    h = ((h << 5) - h) + brut.charCodeAt(i);
                    h |= 0;
                }
                return 'fp_' + Math.abs(h).toString(36) + '_' + this.deviceUuid().substring(0, 8);
            },

            plateforme() {
                const ua = navigator.userAgent;
                if (/android/i.test(ua)) return 'Android';
                if (/iPhone|iPad|iPod/i.test(ua)) return 'iOS';
                if (/Windows/i.test(ua)) return 'Windows';
                if (/Mac/i.test(ua)) return 'MacOS';
                if (/Linux/i.test(ua)) return 'Linux';
                return 'Inconnu';
            },

            navigateur() {
                const ua = navigator.userAgent;
                if (/chrome|chromium|crios/i.test(ua) && !/edg|opr/i.test(ua)) return 'Chrome';
                if (/safari/i.test(ua) && !/chrome|crios/i.test(ua)) return 'Safari';
                if (/firefox|fxios/i.test(ua)) return 'Firefox';
                if (/edg/i.test(ua)) return 'Edge';
                return 'Navigateur';
            },

            submit(form) {
                if (this.busy) return;

                // En retard sans motif : on demande d'abord, on enregistre ensuite.
                if (this.enRetard && this.motif.trim().length < 10) {
                    this.modalRetard = true;
                    return;
                }

                this.capturer(form);
            },

            capturer(form) {
                if (this.busy) return;

                this.erreur = '';
                this.busy   = true;
                this.etape  = 'Localisation en cours…';

                if (!navigator.geolocation) {
                    this.echec("Ce navigateur ne transmet pas la position. Ouvrez la page dans Chrome ou Safari.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.$refs.lat.value      = pos.coords.latitude;
                        this.$refs.lng.value      = pos.coords.longitude;
                        this.$refs.acc.value      = Math.round(pos.coords.accuracy);
                        this.$refs.fp.value       = this.fingerprint();
                        this.$refs.uuid.value     = this.deviceUuid();
                        this.$refs.platform.value = this.plateforme();
                        this.$refs.browser.value  = this.navigateur();
                        this.$refs.label.value    = `${this.plateforme()} (${this.navigateur()})`;

                        this.etape = 'Enregistrement…';
                        form.submit();
                    },
                    (err) => {
                        if (err.code === err.PERMISSION_DENIED) {
                            this.echec("Le pointage a besoin de votre position. Autorisez la localisation dans les réglages de votre navigateur, puis réessayez.");
                        } else if (err.code === err.TIMEOUT) {
                            this.echec("Signal GPS insuffisant, souvent le cas en intérieur. Rapprochez-vous d'une ouverture et réessayez.");
                        } else {
                            this.echec("Position introuvable. Vérifiez que le GPS est activé, puis réessayez.");
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            },

            echec(message) {
                this.busy   = false;
                this.etape  = '';
                this.erreur = message;
            },
        };
    }
</script>
@endpush
@endonce
