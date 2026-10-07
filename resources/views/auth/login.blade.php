@extends('layouts.auth')

@section('title', 'Login — NEVERMORE')

@section('content')
    @if ($errors->any())
        <p class="mt-4 w-full max-w-sm rounded bg-red-100 px-4 py-2 text-center text-sm text-red-700">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 w-full max-w-sm">
        @csrf
        <input name="email" type="text" required value="{{ old('email') }}" placeholder="Username or email"
            class="mb-1 h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <input name="password" type="password" required placeholder="Password"
            class="h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <button class="mt-14 h-16 w-full rounded bg-[#4a7bff] text-2xl font-bold text-white hover:bg-[#3a6bef]">Login</button>
    </form>
    <p class="mt-3 text-center text-base font-bold text-[#1e3a8a]">Don't have an account? <a href="{{ route('register') }}" class="text-[#4a7bff]">Register</a></p>
    <p class="text-center text-base font-bold text-[#1e3a8a]">Forgot your passwrod? <a href="{{ route('password.request') }}" class="text-[#4a7bff]">Reset it</a></p>
@endsection
