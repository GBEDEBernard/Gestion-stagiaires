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

    <style>
        /* Loader overlay */
        #loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #loader-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- Loader -->
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>
    
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loader-overlay");

    // 🔹 Loader sur navigation (sauf boutons avec data-confirm)
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function (e) {
            const href = this.getAttribute("href");
            if (
                href &&
                !href.startsWith("#") &&
                !href.startsWith("javascript") &&
                !this.hasAttribute("data-confirm-edit") && 
                !this.hasAttribute("data-confirm-delete")
            ) {
                loader.classList.add("active");
            }
        });
    });

    // 🔹 Loader sur soumission de formulaire (sauf ceux avec data-confirm-delete)
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function (e) {
            if (!form.hasAttribute("data-confirm-delete")) {
                loader.classList.add("active");
            }
        });
    });

    // 🔹 Confirmation suppression
    document.querySelectorAll("form[data-confirm-delete]").forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "⚠️ Cette action est irréversible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    loader.classList.add("active"); // Loader seulement après confirmation
                    form.submit();
                }
            });
        });
    });

    // 🔹 Confirmation modification
    document.querySelectorAll("a[data-confirm-edit], button[data-confirm-edit]").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const url = this.getAttribute("href");
            Swal.fire({
                title: "Modifier cet élément ?",
                text: "Vous allez passer en mode édition.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Oui, modifier",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed && url) {
                    loader.classList.add("active"); // Loader seulement après confirmation
                    window.location.href = url;
                }
            });
        });
    });
});

// 🔹 Déconnexion après inactivité
(function () {
    const INACTIVITY_LIMIT = 90 * 1000; // 90 sec
    let lastActivity = Date.now();

    function resetTimer() {
        lastActivity = Date.now();
        localStorage.setItem("lastActivity", lastActivity);
    }
    function checkInactivity() {
        const saved = localStorage.getItem("lastActivity") || Date.now();
        const now = Date.now();
        if (now - saved > INACTIVITY_LIMIT) {
            window.location.href = "{{ route('login') }}";
        }
    }
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;

    setInterval(checkInactivity, 10000);
})();
</script>

</body>
</html>
