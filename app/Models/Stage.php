<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'etudiant_id',
        'typestage_id',
        'site_id',
        'supervisor_id',
        'badge_id',
        'domaine_id',
        'theme',
        'date_debut',
        'date_fin',
        'duree_mois',
        'annee_academique',
        'expected_check_in_time',
        'expected_check_out_time',
        'allowed_late_minutes',
        'allowed_early_departure_minutes',
        'break_minutes',
        'presence_mode',
        'follow_up_status',
        'prolonge_stage_id',
        'intitule_poste',
        'filiere',
        'niveau_etude',
        'tuteur_academique',
        'indemnite',
        'livrables',
        'final_report_path',
        'final_report_uploaded_at',
    ];

    protected static function booted()
    {
        static::saving(function ($stage) {
            if ($stage->date_debut) {
                $mois = (int) $stage->date_debut->format('n');
                $annee = (int) $stage->date_debut->format('Y');
                $stage->annee_academique = $mois >= 9
                    ? $annee.'-'.($annee + 1)
                    : ($annee - 1).'-'.$annee;
            }
        });
    }

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'livrables' => 'array',
        'final_report_uploaded_at' => 'datetime',
    ];

    /** Options proposées pour le livrable attendu (fiche de poste). */
    public const LIVERABLES = [
        'Rapport de stage à déposer',
        'Soutenance orale devant jury',
        'Projet / réalisation technique',
        'Documentation des travaux réalisés',
    ];

    /** Valeurs par défaut affichées dans le formulaire de fiche de poste. */
    public const DEFAULT_INTITULE_POSTE = 'Stagiaire académique en développement web';

    public const DEFAULT_INDEMNITE = 'Non rémunéré';

    /** Description lisible du type de stage pour la fiche de poste. */
    public function fichePosteTypeStage(): string
    {
        return match ($this->typestage?->libelle) {
            'Académique' => 'Stage académique de fin de formation (obligatoire pour la validation du diplôme)',
            'Professionnel' => 'Stage professionnel',
            default => $this->typestage?->libelle ?? 'Stage académique de fin de formation (obligatoire pour la validation du diplôme)',
        };
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class, 'typestage_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    public function jours()
    {
        // withPivot : sans cela l'horaire propre au jour reste invisible et la
        // demi-journée du mercredi ne s'applique jamais.
        return $this->belongsToMany(Jour::class, 'stage_jour')
            ->withPivot('start_time', 'end_time', 'break_minutes');
    }

    /**
     * Détermine si le stage prévoit une présence à la date donnée.
     * - Si aucun jour configuré → tous les jours ouvrés sont des jours de travail.
     * - Sinon → uniquement les jours cochés lors de la création du stage.
     */
    public function isWorkDay($date = null): bool
    {
        $date = $date ? \Carbon\Carbon::parse($date) : today();

        $jours = $this->jours;

        if ($jours->isEmpty()) {
            return ! $date->isWeekend();
        }

        $dayName = match ((int) $date->format('N')) {
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
            default => '',
        };

        return $jours->contains(fn ($jour) => strtolower($jour->jour) === strtolower($dayName));
    }

    /**
     * Label lisible des jours de présence du stage (ex. "Lundi, Mardi, Mercredi").
     */
    public function workDaysLabel(): string
    {
        return $this->jours->isNotEmpty()
            ? $this->jours->pluck('jour')->implode(', ')
            : 'Lundi à Vendredi';
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class);
    }

    public function attendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function evaluation()
    {
        return $this->hasOne(StageEvaluation::class);
    }

    public function attestation()
    {
        return $this->hasOne(Attestation::class);
    }

    /**
     * Stage auquel celui-ci prolonge, s'il en est un.
     *
     * Le lien est explicite : deux stages d'un même étudiant qui partagent
     * domaine et type ne sont pas forcément liés. Un stagiaire qui revient un
     * an plus tard dans la même direction fait un NOUVEAU stage, avec sa propre
     * attestation — pas une prolongation d'un an de plus.
     */
    public function stageProlonge()
    {
        return $this->belongsTo(Stage::class, 'prolonge_stage_id');
    }

    /**
     * Segments ajoutés par prolongation de celui-ci.
     */
    public function prolongations()
    {
        return $this->hasMany(Stage::class, 'prolonge_stage_id');
    }

    /**
     * Premier segment de la chaîne : le stage d'origine, pas une prolongation.
     *
     * C'est lui qui porte l'attestation. Les attestations sont adossées au
     * stage d'origine pour qu'une chaîne n'en produise qu'une seule ; une
     * attestation par segment ferait dire au stagiaire qu'il a effectué deux
     * stages alors qu'il n'en a fait qu'un, plus long.
     */
    public function stageRacine(): Stage
    {
        $stage = $this;

        // Garde-fou : une chaîne bouclée ne doit pas faire boucler la page.
        $vus = [];
        while ($stage->prolonge_stage_id && ! isset($vus[$stage->id])) {
            $vus[$stage->id] = true;
            $parent = $stage->stageProlonge()->first();

            if (! $parent) {
                break;
            }

            $stage = $parent;
        }

        return $stage;
    }

    public function estProlongation(): bool
    {
        return (bool) $this->prolonge_stage_id;
    }

    /**
     * Un segment est considéré terminé quand sa date de fin est passée.
     * Un stage sans date de fin n'est jamais terminé : on ne prolonge pas
     * une période qui n'a pas de borne.
     */
    public function estTermine(): bool
    {
        return $this->date_fin !== null && today()->gt($this->date_fin);
    }

    /**
     * La chaîne peut-elle être prolongée ? Il faut que TOUS ses segments
     * soient terminés : prolonger un stage en cours reviendrait à ajouter une
     * période qui chevauche la précédente, et le compte du stagiaire est
     * justement encore actif.
     */
    public function estProlongable(): bool
    {
        return $this->getRelatedSequenceStages()
            ->reject(fn (Stage $segment) => $segment->date_fin === null)
            ->every(fn (Stage $segment) => $segment->estTermine());
    }

    /**
     * Message expliquant pourquoi la prolongation est refusée, ou null si elle
     * est possible. La vue et le contrôleur s'appuient tous les deux dessus
     * pour ne pas diverger.
     */
    public function motifProlongationRefusee(): ?string
    {
        if (! $this->date_fin) {
            return 'Ce stage n’a pas de date de fin : la prolongation n’est pas possible.';
        }

        $segmentsEnCours = $this->getRelatedSequenceStages()
            ->filter(fn (Stage $segment) => $segment->date_fin === null || today()->lte($segment->date_fin))
            ->sortBy(fn (Stage $segment) => $segment->date_fin ?? now()->addCentury())
            ->last();

        if ($segmentsEnCours) {
            $date = $segmentsEnCours->date_fin?->format('d/m/Y') ?? 'sans date';

            return "Le stage est encore en cours (jusqu’au {$date}). Prolonger une période en cours "
                .'ajouterait une période qui la chevauche : terminez-la d’abord.';
        }

        return null;
    }

    /**
     * Tous les segments d'une même chaîne, du plus ancien au plus récent.
     *
     * Seul le stage d'origine est cherché : une prolongation connaît sa
     * racine, donc les segments trouvés ici sont exactement ceux qui doivent
     * être additionnés pour l'attestation.
     */
    public function getRelatedSequenceStages(): \Illuminate\Support\Collection
    {
        if (! $this->etudiant_id) {
            return collect([$this]);
        }

        $racine = $this->stageRacine();

        $segments = static::where(function ($query) use ($racine) {
            $query->where('id', $racine->id)
                ->orWhere('prolonge_stage_id', $racine->id);
        })
            ->whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->orderBy('date_debut')
            ->get();

        return $segments->values();
    }

    public function totalDurationInDays(): int
    {
        $stages = $this->getRelatedSequenceStages();

        if ($stages->isEmpty()) {
            return $this->date_debut && $this->date_fin
                ? (int) $this->date_debut->diffInDays($this->date_fin) + 1
                : 0;
        }

        return (int) $stages->sum(function ($stage) {
            if (! $stage->date_debut || ! $stage->date_fin) {
                return 0;
            }

            return $stage->date_debut->diffInDays($stage->date_fin) + 1;
        });
    }

    public function totalDurationInMonths(): int
    {
        $days = $this->totalDurationInDays();

        return $days > 0 ? max(1, (int) round($days / 30)) : 0;
    }

    public function getFinalReportUrlAttribute(): ?string
    {
        return $this->final_report_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->final_report_path)
            : null;
    }

    public function getFinalReportNameAttribute(): ?string
    {
        return $this->final_report_path
            ? basename($this->final_report_path)
            : null;
    }

    public function getStatutAttribute()
    {
        $today = today();

        if (! $this->date_debut || ! $this->date_fin) {
            return 'A venir';
        }

        if ($today->lt($this->date_debut)) {
            return 'A venir';
        }

        if ($today->gt($this->date_fin)) {
            return 'Termine';
        }

        return 'En cours';
    }
}
