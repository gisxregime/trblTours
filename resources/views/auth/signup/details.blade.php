@extends('layouts.public')

@section('title', 'Complete Signup')

@section('content')
    @php
        $isGuide = $draft->role === 'tour_guide';
        $touristIdTypes = [
            'passport' => 'Passport',
            'national_id' => 'National ID',
            'drivers_license' => "Driver's License",
            'student_id' => 'Student ID',
            'postal_id' => 'Postal ID',
            'voters_id' => "Voter's ID",
            'umid' => 'UMID',
            'prc_id' => 'PRC ID',
            'barangay_id' => 'Barangay ID',
            'other_government_id' => 'Other Government ID',
        ];
        $governmentIdTypes = [
            'national_id' => 'National ID',
            'passport' => 'Passport',
            'drivers_license' => "Driver's License", 
            'other' => 'Other',
        ];
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-[#d4a563]/30 bg-white shadow-[0_22px_60px_-30px_rgba(122,85,50,0.55)]">
            <div class="bg-gradient-to-r from-[#d4a563] via-[#c69958] to-[#8f9d59] px-8 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/80">Step 3 of 3</p>
                <h1 class="mt-2 text-3xl font-bold">Finish your registration</h1>
                <p class="mt-2 max-w-3xl text-sm text-white/90">We already verified {{ $draft->email }} and selected the {{ $isGuide ? 'Tour Guide' : 'Tourist' }} flow. Complete the remaining details below.</p>
            </div>

            <div class="grid gap-8 px-8 py-8 lg:grid-cols-[1fr_320px]">
                <form method="POST" action="{{ route('signup.details.store', ['token' => $draft->token]) }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="full_name" class="mb-2 block text-sm font-semibold text-[#7a5532]">Full name</label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            @error('full_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="mb-2 block text-sm font-semibold text-[#7a5532]">Date of birth</label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            @error('date_of_birth')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="phone_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">Phone number</label>
                            <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30" placeholder="+63 900 000 0000">
                            @error('phone_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="nationality" class="mb-2 block text-sm font-semibold text-[#7a5532]">Nationality</label>
                            <input id="nationality" name="nationality" type="text" value="{{ old('nationality', 'Filipino') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            @error('nationality')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-[#7a5532]">Password</label>
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-[#7a5532]">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-[#d4a563]/30 bg-[#fffaf2] p-5">
                        <h2 class="text-lg font-semibold text-[#7a5532]">Email confirmation</h2>
                        <p class="mt-2 text-sm text-[#8a6746]">Verified email: {{ $draft->email }}</p>
                        <p class="mt-1 text-sm text-[#8a6746]">Selected role: {{ $isGuide ? 'Tour Guide' : 'Tourist' }}</p>
                    </div>

                    @if (! $isGuide)
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="tourist_id_type" class="mb-2 block text-sm font-semibold text-[#7a5532]">Government ID type</label>
                                <select id="tourist_id_type" name="tourist_id_type" required class="w-full rounded-2xl border border-[#d4a563]/45 bg-white px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                                    @foreach ($touristIdTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('tourist_id_type', 'passport') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('tourist_id_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="tourist_id_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">ID number</label>
                                <input id="tourist_id_number" name="tourist_id_number" type="text" value="{{ old('tourist_id_number') }}" class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            </div>
                        </div>
                    @else
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="government_id_type" class="mb-2 block text-sm font-semibold text-[#7a5532]">Government ID type</label>
                                <select id="government_id_type" name="government_id_type" required class="w-full rounded-2xl border border-[#d4a563]/45 bg-white px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                                    @foreach ($governmentIdTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('government_id_type', 'national_id') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('government_id_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="government_id_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">Government ID number</label>
                                <input id="government_id_number" name="government_id_number" type="text" value="{{ old('government_id_number') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                                @error('government_id_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="years_of_experience" class="mb-2 block text-sm font-semibold text-[#7a5532]">Years of experience</label>
                                <input id="years_of_experience" name="years_of_experience" type="number" min="0" value="{{ old('years_of_experience') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                                @error('years_of_experience')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="tour_guide_cert_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">Tour guide certificate number</label>
                                <input id="tour_guide_cert_number" name="tour_guide_cert_number" type="text" value="{{ old('tour_guide_cert_number') }}" class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            </div>

                            <div class="md:col-span-2">
                                <label for="bio" class="mb-2 block text-sm font-semibold text-[#7a5532]">Bio</label>
                                <textarea id="bio" name="bio" rows="4" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">{{ old('bio') }}</textarea>
                                @error('bio')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="nbi_clearance_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">NBI clearance number</label>
                                <input id="nbi_clearance_number" name="nbi_clearance_number" type="text" value="{{ old('nbi_clearance_number') }}" required class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                                @error('nbi_clearance_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="barangay_clearance_number" class="mb-2 block text-sm font-semibold text-[#7a5532]">Barangay clearance number</label>
                                <input id="barangay_clearance_number" name="barangay_clearance_number" type="text" value="{{ old('barangay_clearance_number') }}" class="w-full rounded-2xl border border-[#d4a563]/45 px-4 py-3 text-[#5b3a26] shadow-sm outline-none focus:border-[#c69958] focus:ring-2 focus:ring-[#d4a563]/30">
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3 rounded-[1.5rem] border border-[#d4a563]/30 bg-[#fffaf2] p-5">
                        <label class="flex items-start gap-3 text-sm text-[#6f5d52]">
                            <input type="checkbox" name="terms_agreed" value="1" class="mt-1 rounded border-[#c69958] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" {{ old('terms_agreed') ? 'checked' : '' }} required>
                            <span>I agree to the Terms &amp; Conditions and Privacy Policy.</span>
                        </label>

                        <label class="flex items-start gap-3 text-sm text-[#6f5d52]">
                            <input type="checkbox" name="identity_consent" value="1" class="mt-1 rounded border-[#c69958] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" {{ old('identity_consent') ? 'checked' : '' }} required>
                            <span>I consent to identity verification for this account.</span>
                        </label>

                        @if ($isGuide)
                            <label class="flex items-start gap-3 text-sm text-[#6f5d52]">
                                <input type="checkbox" name="pending_understood" value="1" class="mt-1 rounded border-[#c69958] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" {{ old('pending_understood') ? 'checked' : '' }} required>
                                <span>I understand my guide account will stay pending until the team reviews my details.</span>
                            </label>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('signup.role', ['token' => $draft->token]) }}" class="rounded-full border border-[#d4a563]/45 px-6 py-3 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">Back</a>
                        <button type="submit" class="inline-flex items-center rounded-full bg-[#8f9d59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Create account
                        </button>
                    </div>
                </form>

                <aside class="rounded-[1.5rem] border border-[#d4a563]/30 bg-[#fffaf2] p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a6746]">Summary</p>
                    <h2 class="mt-3 text-xl font-bold text-[#7a5532]">{{ $draft->email }}</h2>
                    <p class="mt-2 text-sm text-[#8a6746]">{{ $isGuide ? 'Tour Guide registration' : 'Tourist registration' }}</p>
                    <div class="mt-6 space-y-3 text-sm text-[#6f5d52]">
                        <p>Step 1: Email verified</p>
                        <p>Step 2: Role selected</p>
                        <p>Step 3: Profile details</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection