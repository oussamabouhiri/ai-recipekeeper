<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - AI Recipe Keeper</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background antialiased min-h-screen flex flex-col selection:bg-primary/20 selection:text-primary">

    <header class="hidden md:block top-0 z-50 w-full bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 shadow-sm">
        <div class="flex justify-between items-center px-6 py-3 w-full max-w-7xl mx-auto">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-headline-md text-headline-md font-bold text-primary">AI Recipe Keeper</a>
                <nav class="flex items-center gap-6 ml-8 font-body-md text-body-md" aria-label="Main navigation">
                    <a href="{{ route('dashboard') }}" class="text-on-surface-variant hover:text-primary transition-colors">Dashboard</a>
                    <a href="{{ route('recipes.browse') }}" class="text-on-surface-variant hover:text-primary transition-colors">Browse</a>
                    <a href="{{ route('recipes.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">My Recipes</a>
                    <a href="{{ route('generations.create') }}" class="text-on-surface-variant hover:text-primary transition-colors">Generate with AI</a>
                    <a href="{{ route('favorites.index') }}" class="{{ request()->routeIs('favorites.index') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors' }}">My Favorites</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('recipes.create') }}" class="bg-primary text-on-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    Create Recipe
                </a>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary/15 text-primary flex items-center justify-center font-label-md text-label-md border border-primary/20" title="{{ $user->name }}">{{ $initials }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="grow w-full max-w-7xl mx-auto px-6 py-8 md:py-12 flex flex-col gap-12 pb-24 md:pb-12">
        @if (session('success'))
            <div class="rounded-lg bg-primary-container/10 border border-primary-container/30 p-4 text-on-surface text-body-md">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="w-full border-t border-outline-variant/20 bg-surface-container-lowest md:mb-0 mb-20">
        <div class="max-w-7xl mx-auto px-6 py-2 flex flex-col md:flex-row justify-between items-center gap-3 pt-8 pb-12 md:py-8">
            <div class="font-headline-md text-headline-md text-primary">AI Recipe Keeper</div>
            <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2 font-caption text-caption" aria-label="Footer navigation">
                <a href="{{ route('dashboard') }}" class="text-on-surface-variant hover:text-primary transition-colors">Dashboard</a>
                <a href="{{ route('recipes.browse') }}" class="text-on-surface-variant hover:text-primary transition-colors">Browse</a>
                <a href="{{ route('recipes.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">My Recipes</a>
                <a href="{{ route('generations.create') }}" class="text-on-surface-variant hover:text-primary transition-colors">Generate with AI</a>
                <a href="{{ route('favorites.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">My Favorites</a>
            </nav>
            <div class="font-caption text-caption text-on-surface-variant/60">
                © {{ date('Y') }} AI Recipe Keeper. Crafted for the modern kitchen.
            </div>
        </div>
    </footer>

    <nav class="md:hidden fixed bottom-0 w-full z-50 border-t border-outline-variant/30 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] bg-surface/90 backdrop-blur-lg px-2 py-3" aria-label="Mobile navigation">
        <div class="flex justify-around items-center max-w-md mx-auto w-full">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 transition-colors">
                <span class="material-symbols-outlined mb-1">dashboard</span>
                <span class="font-caption text-caption">Home</span>
            </a>
            <a href="{{ route('recipes.browse') }}" class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 transition-colors">
                <span class="material-symbols-outlined mb-1">explore</span>
                <span class="font-caption text-caption">Browse</span>
            </a>
            <a href="{{ route('generations.create') }}" class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 transition-colors">
                <span class="material-symbols-outlined mb-1">auto_awesome</span>
                <span class="font-caption text-caption">AI Gen</span>
            </a>
            <a href="{{ route('favorites.index') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('favorites.index') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant' }} px-4 py-1 rounded-xl transition-colors">
                <span class="material-symbols-outlined mb-1" {{ request()->routeIs('favorites.index') ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' }}>favorite</span>
                <span class="font-caption text-caption {{ request()->routeIs('favorites.index') ? 'font-bold' : '' }}">Favs</span>
            </a>
        </div>
    </nav>

    @yield('scripts')
</body>
</html>
