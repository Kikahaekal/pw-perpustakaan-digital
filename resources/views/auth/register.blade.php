@extends('layouts.auth')

@section('title', 'Register — NEVERMORE')

@section('content')
    @if ($errors->any())
        <div class="mt-4 w-full max-w-sm rounded bg-red-100 px-4 py-2 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-6 w-full max-w-sm">
        @csrf
        <input name="fullname" required value="{{ old('fullname') }}" placeholder="Username"
            class="mb-1 h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <input name="password" type="password" required placeholder="Password"
            class="mb-1 h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <input name="password_confirmation" type="password" required placeholder="Confirm Password"
            class="mb-1 h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <input name="email" type="email" required value="{{ old('email') }}" placeholder="Email"
            class="h-14 w-full rounded bg-[#cfcfcf] px-5 text-base font-semibold text-slate-700 placeholder-[#8a8a8a] focus:outline-none">
        <button class="mt-6 h-16 w-full rounded bg-[#4a7bff] text-2xl font-bold text-white hover:bg-[#3a6bef]">Register</button>
    </form>
    <p class="mt-4 text-center text-base font-bold text-[#1e3a8a]">Already have an account? <a href="{{ route('login') }}" class="text-[#4a7bff]">Login</a></p>
@endsection
