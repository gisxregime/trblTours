@extends('layouts.public')

@section('title', 'Choose Your Role')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-[#d4a563]/30 bg-white shadow-[0_22px_60px_-30px_rgba(122,85,50,0.55)]">
            <div class="bg-gradient-to-r from-[#d4a563] via-[#c69958] to-[#8f9d59] px-8 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/80">Step 2 of 3</p>
                <h1 class="mt-2 text-3xl font-bold">Choose your role</h1>
                <p class="mt-2 max-w-2xl text-sm text-white/90">Your email has been verified for {{ $draft->email }}. Pick the account type you want to continue with.</p>
            </div>

            <div class="px-8 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-[#8f9d59]/30 bg-[#f4f8ea] px-4 py-3 text-sm text-[#556b2f]">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('signup.role.store', ['token' => $draft->token]) }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="rounded-2xl border border-[#d4a563]/40 p-5 transition hover:bg-[#fff8eb]">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="role" value="tourist" class="mt-1 h-4 w-4 border-[#c69958] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" {{ old('role', 'tourist') === 'tourist' ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#8a6746]">Tourist</p>
                                    <p class="mt-2 text-sm text-[#6f5d52]">Book local experiences and explore with a guide who knows the terrain.</p>
                                </div>
                            </div>
                        </label>

                        <label class="rounded-2xl border border-[#d4a563]/40 p-5 transition hover:bg-[#fff8eb]">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="role" value="tour_guide" class="mt-1 h-4 w-4 border-[#c69958] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" {{ old('role') === 'tour_guide' ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#8a6746]">Tour Guide</p>
                                    <p class="mt-2 text-sm text-[#6f5d52]">Offer local tours, share your hometown, and manage your guide profile.</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('role')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('signup.start') }}" class="text-sm font-semibold text-[#7a5532] hover:underline">Start over</a>
                        <button type="submit" class="inline-flex items-center rounded-full bg-[#8f9d59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection