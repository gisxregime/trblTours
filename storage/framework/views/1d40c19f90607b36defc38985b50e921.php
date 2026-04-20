<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'TrblTours')); ?> - Sign Up</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Cormorant+SC:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        .brand-font {
            font-family: 'Asimovian', 'Instrument Sans', sans-serif;
            letter-spacing: 0.02em;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(212, 165, 99, 0.72), rgba(184, 195, 136, 0.56)), url('/images/signup_login_bg.jpg') center/cover no-repeat fixed;
            color: #6f5d52;
        }

        .brand-header {
            position: fixed;
            top: clamp(10px, 2.2vw, 24px);
            left: clamp(10px, 2.2vw, 24px);
            display: flex;
            align-items: center;
            gap: clamp(8px, 1vw, 12px);
            z-index: 100;
        }

        .brand-icon {
            width: clamp(34px, 4.2vw, 50px);
            height: clamp(34px, 4.2vw, 50px);
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .brand-text {
            font-size: clamp(14px, 1.8vw, 20px);
            font-weight: 700;
            color: #fffaf0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .panel {
            width: 100%;
            max-width: 560px;
            background: rgba(255, 251, 244, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(101, 67, 33, 0.22);
            padding: 34px;
            border: 1px solid rgba(139, 69, 19, 0.08);
        }

        .role-toggle {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 14px 0 20px;
        }

        .role-btn {
            border: 1.5px solid #d8c9b5;
            border-radius: 12px;
            padding: 14px 12px;
            background: #f7efe1;
            color: #6f5d52;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .role-btn.active {
            border-color: #8b4513;
            box-shadow: 0 6px 14px rgba(139, 69, 19, 0.12);
        }
    </style>
</head>
<body>
    <?php
        $governmentIdTypes = [
            'national_id' => 'National ID',
            'passport' => 'Passport',
            'drivers_license' => "Driver's License",
            'other' => 'Other',
        ];

        $nationalities = [
            'Afghan', 'Albanian', 'Algerian', 'Andorran', 'Angolan', 'Antiguan and Barbudan', 'Argentine', 'Armenian', 'Australian',
            'Austrian', 'Azerbaijani', 'Bahamian', 'Bahraini', 'Bangladeshi', 'Barbadian', 'Belarusian', 'Belgian', 'Belizean',
            'Beninese', 'Bhutanese', 'Bolivian', 'Bosnian and Herzegovinian', 'Botswanan', 'Brazilian', 'Bruneian', 'Bulgarian',
            'Burkinabe', 'Burundian', 'Cabo Verdean', 'Cambodian', 'Cameroonian', 'Canadian', 'Central African', 'Chadian',
            'Chilean', 'Chinese', 'Colombian', 'Comoran', 'Congolese', 'Costa Rican', 'Croatian', 'Cuban', 'Cypriot', 'Czech',
            'Danish', 'Djiboutian', 'Dominican', 'Dutch', 'East Timorese', 'Ecuadorean', 'Egyptian', 'Emirati', 'Equatorial Guinean',
            'Eritrean', 'Estonian', 'Eswatini', 'Ethiopian', 'Fijian', 'Filipino', 'Finnish', 'French', 'Gabonese', 'Gambian',
            'Georgian', 'German', 'Ghanaian', 'Greek', 'Grenadian', 'Guatemalan', 'Guinean', 'Guyanese', 'Haitian', 'Honduran',
            'Hungarian', 'Icelandic', 'Indian', 'Indonesian', 'Iranian', 'Iraqi', 'Irish', 'Israeli', 'Italian', 'Ivorian',
            'Jamaican', 'Japanese', 'Jordanian', 'Kazakh', 'Kenyan', 'Kiribati', 'Kuwaiti', 'Kyrgyz', 'Lao', 'Latvian', 'Lebanese',
            'Liberian', 'Libyan', 'Liechtensteiner', 'Lithuanian', 'Luxembourger', 'Malagasy', 'Malawian', 'Malaysian', 'Maldivian',
            'Malian', 'Maltese', 'Marshallese', 'Mauritanian', 'Mauritian', 'Mexican', 'Micronesian', 'Moldovan', 'Monacan',
            'Mongolian', 'Montenegrin', 'Moroccan', 'Mozambican', 'Namibian', 'Nauruan', 'Nepalese', 'New Zealander', 'Nicaraguan',
            'Nigerian', 'Nigerien', 'North Korean', 'North Macedonian', 'Norwegian', 'Omani', 'Pakistani', 'Palauan', 'Panamanian',
            'Papua New Guinean', 'Paraguayan', 'Peruvian', 'Polish', 'Portuguese', 'Qatari', 'Romanian', 'Russian', 'Rwandan',
            'Saint Kitts and Nevis', 'Saint Lucian', 'Saint Vincentian', 'Samoan', 'San Marinese', 'Sao Tomean', 'Saudi',
            'Senegalese', 'Serbian', 'Seychellois', 'Sierra Leonean', 'Singaporean', 'Slovak', 'Slovenian', 'Solomon Islander',
            'Somali', 'South African', 'South Korean', 'South Sudanese', 'Spanish', 'Sri Lankan', 'Sudanese', 'Surinamese',
            'Swedish', 'Swiss', 'Syrian', 'Taiwanese', 'Tajik', 'Tanzanian', 'Thai', 'Togolese', 'Tongan', 'Trinidadian and Tobagonian',
            'Tunisian', 'Turkish', 'Turkmen', 'Tuvaluan', 'Ugandan', 'Ukrainian', 'Uruguayan', 'Uzbek', 'Vanuatuan', 'Venezuelan',
            'Vietnamese', 'Yemeni', 'Zambian', 'Zimbabwean',
        ];
    ?>

    <div class="brand-header">
        <img src="<?php echo e(asset('images/tribaltours_icon.png')); ?>" alt="TrblTours" class="brand-icon">
        <div class="brand-text brand-font">TrblTours</div>
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <section class="panel">
            <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 rounded-full border border-[#c9b59f] bg-[#f4efe6] px-3 py-1.5 text-[13px] font-semibold text-[#3f2d1f] shadow-sm transition hover:bg-[#ece1d0] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#8b4e1c]/30">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                <span>Back to Home</span>
            </a>

            <div class="mt-3 mb-4">
                <h3 class="text-2xl font-semibold tracking-tight text-[#23170f]">Create Your Account</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 1): ?>
                    <p class="mt-1 text-sm text-[#584637]">Step 1 of 4: Enter your email.</p>
                <?php elseif($step === 2): ?>
                    <p class="mt-1 text-sm text-[#584637]">Step 2 of 4: Enter the OTP code sent to your email.</p>
                <?php elseif($step === 3): ?>
                    <p class="mt-1 text-sm text-[#584637]">Step 3 of 4: Choose your role.</p>
                <?php else: ?>
                    <p class="mt-1 text-sm text-[#584637]">Step 4 of 4: Complete your profile details.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('errors')?->any()): ?>
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    <?php echo e(session('errors')->first()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 1): ?>
                <form method="POST" action="<?php echo e(route('signup.email.store')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <p class="border-b border-[#d4c5b2] pb-2 text-[11px] font-semibold uppercase tracking-[0.09em] text-[#604c3a]">Account Verification</p>

                    <div>
                        <label for="email" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Email Address <span class="text-[#9a4f1d]">*</span></label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="<?php echo e(old('email')); ?>"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="example@email.com"
                            class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition placeholder:text-[#958067] focus:border-[#9a5f2a] focus:ring-0"
                        >
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full rounded-full bg-[#8b4e1c] px-4 py-2.5 text-sm font-semibold text-[#f8efe3] transition hover:bg-[#764116]"
                    >
                        Send OTP
                    </button>

                    <p class="pt-1 text-center text-sm text-[#6f5b46]">
                        Already have an account?
                        <a href="<?php echo e(route('login')); ?>" class="font-medium text-[#4b3828] hover:underline">Log in</a>
                    </p>
                </form>
            <?php elseif($step === 2 && $draft): ?>
                <form method="POST" action="<?php echo e(route('signup.otp.store', ['token' => $draft->token])); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <p class="border-b border-[#d4c5b2] pb-2 text-[11px] font-semibold uppercase tracking-[0.09em] text-[#604c3a]">OTP Verification</p>
                    <p class="text-sm text-[#6f5b46]">Enter the 6-digit code sent to <?php echo e($draft->email); ?>.</p>

                    <div>
                        <label for="otp_code" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Verification Code <span class="text-[#9a4f1d]">*</span></label>
                        <input
                            id="otp_code"
                            type="text"
                            name="otp_code"
                            value="<?php echo e(old('otp_code')); ?>"
                            maxlength="6"
                            required
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="123456"
                            class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition placeholder:text-[#958067] focus:border-[#9a5f2a] focus:ring-0"
                        >
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full rounded-full bg-[#8b4e1c] px-4 py-2.5 text-sm font-semibold text-[#f8efe3] transition hover:bg-[#764116]"
                    >
                        Verify OTP
                    </button>
                </form>

                <form method="POST" action="<?php echo e(route('signup.otp.resend', ['token' => $draft->token])); ?>" class="mt-3">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full rounded-full border border-[#d8c9b5] bg-[#f7efe1] px-4 py-2.5 text-sm font-semibold text-[#6f5b46] transition hover:bg-[#efe4d5]">
                        Resend OTP
                    </button>
                </form>
            <?php elseif($step === 3 && $draft): ?>
                <form method="POST" action="<?php echo e(route('signup.role.store', ['token' => $draft->token])); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <p class="border-b border-[#d4c5b2] pb-2 text-[11px] font-semibold uppercase tracking-[0.09em] text-[#604c3a]">Role Selection</p>
                    <p class="text-sm text-[#6f5b46]">Verified email: <?php echo e($draft->email); ?></p>

                    <div class="role-toggle">
                        <label class="role-btn <?php echo e(old('role', 'tourist') === 'tourist' ? 'active' : ''); ?>" for="role_tourist">
                            <input id="role_tourist" type="radio" name="role" value="tourist" class="sr-only" <?php echo e(old('role', 'tourist') === 'tourist' ? 'checked' : ''); ?>>
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-[#efe4d5] text-[#8b4e1c]">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="text-sm font-medium">Tourist</span>
                        </label>

                        <label class="role-btn <?php echo e(old('role') === 'tour_guide' ? 'active' : ''); ?>" for="role_guide">
                            <input id="role_guide" type="radio" name="role" value="tour_guide" class="sr-only" <?php echo e(old('role') === 'tour_guide' ? 'checked' : ''); ?>>
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-[#efe4d5] text-[#8b4e1c]">
                                <i class="fas fa-map"></i>
                            </div>
                            <span class="text-sm font-medium">Tour Guide</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full rounded-full bg-[#8b4e1c] px-4 py-2.5 text-sm font-semibold text-[#f8efe3] transition hover:bg-[#764116]"
                    >
                        Continue
                    </button>
                </form>
            <?php elseif($step === 4 && $draft): ?>
                <form method="POST" action="<?php echo e(route('signup.details.store', ['token' => $draft->token])); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <p class="border-b border-[#d4c5b2] pb-2 text-[11px] font-semibold uppercase tracking-[0.09em] text-[#604c3a]">Profile Details</p>
                    <p class="text-sm text-[#6f5b46]">Verified email: <?php echo e($draft->email); ?> | Role: <?php echo e($isGuide ? 'Tour Guide' : 'Tourist'); ?></p>

                    <div>
                        <label for="full_name" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Full Name <span class="text-[#9a4f1d]">*</span></label>
                        <input id="full_name" name="full_name" type="text" value="<?php echo e(old('full_name')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Password <span class="text-[#9a4f1d]">*</span></label>
                            <input id="password" name="password" type="password" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Confirm Password <span class="text-[#9a4f1d]">*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="date_of_birth" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Date of Birth <span class="text-[#9a4f1d]">*</span></label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="<?php echo e(old('date_of_birth')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                        </div>
                        <div>
                            <label for="phone_number" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Phone Number <span class="text-[#9a4f1d]">*</span></label>
                            <input id="phone_number" name="phone_number" type="text" value="<?php echo e(old('phone_number')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                        </div>
                    </div>

                    <div>
                        <label for="nationality" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Nationality <span class="text-[#9a4f1d]">*</span></label>
                        <select id="nationality" name="nationality" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $nationalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nationality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($nationality); ?>" <?php if(old('nationality', 'Filipino') === $nationality): echo 'selected'; endif; ?>><?php echo e($nationality); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isGuide): ?>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="government_id_type" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Government ID Type <span class="text-[#9a4f1d]">*</span></label>
                                <select id="government_id_type" name="government_id_type" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $governmentIdTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php if(old('government_id_type', 'national_id') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label for="government_id_number" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Government ID Number <span class="text-[#9a4f1d]">*</span></label>
                                <input id="government_id_number" name="government_id_number" type="text" value="<?php echo e(old('government_id_number')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="years_of_experience" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Years of Experience <span class="text-[#9a4f1d]">*</span></label>
                                <input id="years_of_experience" name="years_of_experience" type="number" min="0" value="<?php echo e(old('years_of_experience')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            </div>
                            <div>
                                <label for="tour_guide_cert_number" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Tour Guide Certificate Number</label>
                                <input id="tour_guide_cert_number" name="tour_guide_cert_number" type="text" value="<?php echo e(old('tour_guide_cert_number')); ?>" class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Bio <span class="text-[#9a4f1d]">*</span></label>
                            <textarea id="bio" name="bio" rows="4" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0"><?php echo e(old('bio')); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="nbi_clearance_number" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">NBI Clearance Number <span class="text-[#9a4f1d]">*</span></label>
                                <input id="nbi_clearance_number" name="nbi_clearance_number" type="text" value="<?php echo e(old('nbi_clearance_number')); ?>" required class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            </div>
                            <div>
                                <label for="barangay_clearance_number" class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-[#5c4a3a]">Barangay Clearance Number</label>
                                <input id="barangay_clearance_number" name="barangay_clearance_number" type="text" value="<?php echo e(old('barangay_clearance_number')); ?>" class="w-full rounded-lg border border-[#d8c9b5] bg-[#f4efe6] px-3 py-2.5 text-sm text-[#2f241a] outline-none transition focus:border-[#9a5f2a] focus:ring-0">
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="space-y-2 rounded-lg border border-[#d8c9b5] bg-[#f4efe6] p-3 text-sm text-[#6f5b46]">
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="terms_agreed" value="1" class="mt-1 h-4 w-4 rounded border-[#c8b49b] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" <?php echo e(old('terms_agreed') ? 'checked' : ''); ?> required>
                            <span>I agree to the Terms and Conditions and Privacy Policy.</span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="identity_consent" value="1" class="mt-1 h-4 w-4 rounded border-[#c8b49b] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" <?php echo e(old('identity_consent') ? 'checked' : ''); ?> required>
                            <span>I consent to identity verification.</span>
                        </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isGuide): ?>
                            <label class="flex items-start gap-2">
                                <input type="checkbox" name="pending_understood" value="1" class="mt-1 h-4 w-4 rounded border-[#c8b49b] text-[#8b4e1c] focus:ring-[#8b4e1c]/30" <?php echo e(old('pending_understood') ? 'checked' : ''); ?> required>
                                <span>I understand my guide account will be pending approval.</span>
                            </label>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full rounded-full bg-[#8b4e1c] px-4 py-2.5 text-sm font-semibold text-[#f8efe3] transition hover:bg-[#764116]"
                    >
                        Create Account
                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    </main>

    <script>
        const roleInputs = document.querySelectorAll('input[name="role"]');

        roleInputs.forEach((input) => {
            input.addEventListener('change', () => {
                document.querySelectorAll('.role-btn').forEach((button) => button.classList.remove('active'));
                input.closest('.role-btn')?.classList.add('active');
            });
        });
    </script>
</body>
</html>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/auth/signup/flow.blade.php ENDPATH**/ ?>