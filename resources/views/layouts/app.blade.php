<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-sans antialiased">
    
    <div class="h-screen bg-cover bg-center bg-no-repeat relative"
         style="background-image: url('{{ asset('images/TGFpdf.jpg') }}'); background-size: cover; background-position: center;">
     
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

  <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Confirmation suppression
    document.querySelectorAll("form[data-confirm-delete]").forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // Empêche la soumission immédiate
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "⚠️ Cette action est irréversible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f", // rouge
                cancelButtonColor: "#6c757d", // gris
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Soumettre seulement si confirmé
                }
            });
        });
    });

    // Confirmation modification
    document.querySelectorAll("a[data-confirm-edit], button[data-confirm-edit]").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const url = this.getAttribute("href");
            Swal.fire({
                title: "Modifier cet élément ?",
                text: "Vous allez passer en mode édition.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6", // bleu
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Oui, modifier",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed && url) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>
<!-- la redirectionsur la page connexion apres des heure et de minuite -->
<script>
(function () {
    const INACTIVITY_LIMIT = 90 * 1000; // 90 secondes (1min30)
    let lastActivity = Date.now();

    // Fonction pour mettre à jour l'activité
    function resetTimer() {
        lastActivity = Date.now();
        localStorage.setItem("lastActivity", lastActivity);
    }

    // Vérifier régulièrement si le temps est dépassé
    function checkInactivity() {
        const saved = localStorage.getItem("lastActivity") || Date.now();
        const now = Date.now();

        if (now - saved > INACTIVITY_LIMIT) {
            // 🔥 Redirection forcée vers login
            window.location.href = "{{ route('login') }}";
        }
    }

    // On surveille les mouvements/clavier/clics
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;

    // Vérifie toutes les 10 secondes
    setInterval(checkInactivity, 10000);
})();
</script>

</body>
</html>
