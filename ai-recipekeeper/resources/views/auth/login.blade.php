@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h2 class="h5 text-center mb-4">Sign in to your account</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input id="remember" type="checkbox" class="form-check-input" name="remember">
                    <label for="remember" class="form-check-label">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Log in</button>
            </form>
        </div>
    </div>

    <div class="text-center mt-3">
        <span class="text-muted">Don&apos;t have an account?</span>
        <a href="{{ route('register') }}" class="text-decoration-none">Sign up</a>
    </div>
@endsection