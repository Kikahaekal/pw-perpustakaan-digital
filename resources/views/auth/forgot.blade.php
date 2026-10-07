@extends('layouts.auth')

@section('title', 'Reset Password — NEVERMORE')

@section('content')
    @if (session('status'))
        <p class="mt-4 w-full max-w-sm rounded bg-emerald-100 px-4 py-2 text-center text-sm text-emerald-700">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <p class="mt-4 w-full max-w-sm rounded bg-red-100 px-4 py-2 text-center text-sm text-red-700">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 w-full max-w-sm">
        @csrf
        <input name="email" type="text" required value="{{ old('email') }}" placeholder="Username or email"
            class="h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <button class="mt-14 flex h-16 w-full items-center justify-center gap-2 rounded bg-[#4a7bff] text-2xl font-bold text-white hover:bg-[#3a6bef]">
            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="#fff"><path d="M2 5h20v14H2V5zm2 2l8 6 8-6H4z"/></svg>
            Send Reset Link
        </button>
    </form>
    <p class="mt-3 text-center text-base font-bold text-[#1e3a8a]">Remembered your password? <a href="{{ route('login') }}" class="text-[#4a7bff]">Login</a></p>
@endsection
