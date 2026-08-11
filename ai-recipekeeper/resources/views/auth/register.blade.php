@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="text-center mb-6">
        <h1 class="font-display-lg text-[48px] leading-[56px] tracking-tight font-bold text-primary mb-1 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
            AI Recipe Keeper
        </h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Crafted for the modern kitchen.</p>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-outline-variant/30 p-6 md:p-8 backdrop-blur-sm relative overflow-hidden">
        <div class="mb-6 text-center">
            <h2 class="font-headline-md text-[24px] leading-[32px] font-semibold text-on-surface mb-2">Join the Kitchen</h2>
            <p class="font-body-md text-on-surface-variant">Create your account to start saving and generating recipes.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-error-container/20 border border-error-container/40 p-4">
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

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-label-md text-label-md text-on-surface mb-1">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Julia Child"
                        class="block w-full pl-10 pr-3 py-3 bg-surface border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 outline-none @error('name') border-error @enderror"
                    >
                </div>
                @error('name')
                    <p class="text-sm text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block font-label-md text-label-md text-on-surface mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="julia@kitchen.com"
                        class="block w-full pl-10 pr-3 py-3 bg-surface border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 outline-none @error('email') border-error @enderror"
                    >
                </div>
                @error('email')
                    <p class="text-sm text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block font-label-md text-label-md text-on-surface mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="block w-full pl-10 pr-10 py-3 bg-surface border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 outline-none @error('password') border-error @enderror"
                    >
                    <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant hover:text-on-surface transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-xl">visibility_off</span>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block font-label-md text-label-md text-on-surface mb-1">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50">
                        <span class="material-symbols-outlined">lock_reset</span>
                    </div>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="block w-full pl-10 pr-10 py-3 bg-surface border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 outline-none"
                    >
                    <button type="button" data-toggle-password="password_confirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant hover:text-on-surface transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-xl">visibility_off</span>
                    </button>
                </div>
            </div>

            <div class="pt-1">
                <button type="submit" class="w-full bg-primary text-on-primary font-label-md text-label-md py-3 px-4 rounded-lg hover:bg-surface-tint focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 shadow-sm active:scale-[0.98] flex items-center justify-center gap-2 group">
                    Create Account
                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
        </form>

        <div class="mt-6 text-center border-t border-outline-variant/20 pt-4">
            <p class="font-body-md text-body-md text-on-surface-variant">
                Already have an account?
                <a class="font-label-md text-label-md text-primary hover:text-surface-tint hover:underline transition-colors ml-1" href="{{ route('login') }}">Log in</a>
            </p>
        </div>
    </div>
@endsection
