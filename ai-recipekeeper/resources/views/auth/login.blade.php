@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="bg-surface-container-lowest rounded-xl auth-shadow border border-surface-container p-8 sm:p-10 relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-32 h-32 bg-primary-fixed/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container-low mb-3 text-primary">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            </div>
            <h1 class="font-headline-lg text-[32px] leading-[40px] font-bold text-on-surface mb-1 hidden md:block">AI Recipe Keeper</h1>
            <h1 class="font-headline-lg text-[28px] leading-[36px] font-bold text-on-surface mb-1 md:hidden">AI Recipe Keeper</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Welcome back to your kitchen.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-error-container/20 border border-error-container/40 p-4 relative z-10">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm text-error flex items-start gap-2">
                            <span class="material-symbols-outlined text-base mt-0.5">error</span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
            @csrf

            <div class="space-y-1">
                <label for="email" class="block font-label-md text-label-md text-on-surface">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined text-xl">mail</span>
                    </div>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="chef@example.com"
                        class="block w-full pl-10 pr-3 py-3 border border-outline-variant rounded-lg bg-background text-on-surface focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors font-body-md text-body-md placeholder-on-surface-variant/50 @error('email') border-error @enderror"
                    >
                </div>
                @error('email')
                    <p class="text-sm text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label for="password" class="block font-label-md text-label-md text-on-surface">Password</label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="block w-full pl-10 pr-10 py-3 border border-outline-variant rounded-lg bg-background text-on-surface focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors font-body-md text-body-md placeholder-on-surface-variant/50 @error('password') border-error @enderror"
                    >
                    <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant hover:text-on-surface transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-xl">visibility_off</span>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 text-primary focus:ring-primary border-outline-variant rounded bg-background cursor-pointer"
                >
                <label for="remember" class="ml-2 block font-body-md text-body-md text-on-surface-variant cursor-pointer">
                    Remember me
                </label>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm font-label-md text-label-md text-on-primary bg-primary hover:bg-surface-tint focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors active:scale-[0.98] duration-200">
                    Log In
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-center">
        <p class="font-body-md text-body-md text-on-surface-variant">
            Don't have an account?
            <a class="font-label-md text-label-md text-primary hover:text-surface-tint transition-colors underline underline-offset-4 decoration-primary/30 hover:decoration-primary" href="{{ route('register') }}">Sign up</a>
        </p>
    </div>
@endsection
