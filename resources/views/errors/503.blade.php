<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dienst nicht verfügbar</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts für eine ansprechendere Typografie -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased text-white">
    <div class="relative flex items-center justify-center min-h-screen overflow-hidden">
        <!-- Background Video -->
        <video autoplay loop muted class="absolute z-0 w-auto min-w-full min-h-full max-w-none">
            <source src="/img/59987c28-310f-4558-8aad-d15af2ae8265.mp4" type="video/mp4">
            Ihr Browser unterstützt das Video-Tag nicht.
        </video>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black opacity-60"></div>

        <div class="relative z-10 max-w-xl w-full mx-auto p-6 lg:p-8 text-center">

            <!-- Error Code -->
            <p class="text-5xl font-bold text-blue-400">503</p>

            <!-- Title -->
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Dienst vorübergehend nicht verfügbar
            </h1>

            <!-- Message -->
            <p class="mt-6 text-base leading-7 text-gray-300">
                Wir führen zurzeit geplante Wartungsarbeiten durch. Wir sollten in Kürze wieder online sein. Vielen Dank für Ihre Geduld!
            </p>

            <!-- Action Button -->
            <div class="mt-10">
                <a href="/" class="rounded-md bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors duration-200">
                    Zurück zur Startseite
                </a>
            </div>

        </div>
    </div>
</body>

</html>