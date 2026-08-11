<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI Recipe Keeper') - AI Recipe Keeper</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen flex items-center justify-center p-6 font-body-md text-on-background antialiased" style="background-image: url('{{ asset('images/auth/image.png') }}'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed;">

    <main class="w-full max-w-md relative z-10">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-primary-container/10 border border-primary-container/30 p-4 text-on-surface text-body-md">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>

</body>
</html>
