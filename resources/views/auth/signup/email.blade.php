@extends('layouts.public')

@section('title', 'Create Your Account')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-[#d4a563]/30 bg-white shadow-[0_22px_60px_-30px_rgba(122,85,50,0.55)]">
            <div class="bg-gradient-to-r from-[#d4a563] via-[#c69958] to-[#8f9d59] px-8 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/80">Step 1 of 3</p>
                <h1 class="mt-2 text-3xl font-bold">Verify your email first</h1>
                <p class="mt-2 max-w-2xl text-sm text-white/90">We send a secure verification link before you choose your role or continue with the rest of signup.</p>
            </div>

            <div class="px-8 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-[#8f9d59]/30 bg-[#f4f8ea] px-4 py-3 text-sm text-[#556b2f]">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('signup.email.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-[#7a5532]">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full rounded-2xl border border-[#d4a563]/45 bg-white px-4 py-3 text-[#5b3a26] shadow-sm outline-none transition focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30"
                            placeholder="you@example.com"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-[#8a6746]">Already verified? You can continue from your email link.</p>
                        <button type="submit" class="inline-flex items-center rounded-full bg-[#8f9d59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Send verification link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection