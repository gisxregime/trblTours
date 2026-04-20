<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'TrblTours')); ?> - Forgot Password</title>

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
    </style>
</head>
<body>
    <div class="brand-header">
        <img src="<?php echo e(asset('images/tribaltours_icon.png')); ?>" alt="TrblTours" class="brand-icon">
        <div class="brand-text brand-font">TrblTours</div>
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <section class="panel">
            <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 rounded-full border border-[#d8c9b5] bg-[#f4efe6] px-3 py-1.5 text-[13px] font-medium text-[#4b3828] shadow-sm transition hover:bg-[#ece1d0]">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                <span>Back to Home</span>
            </a>

            <div class="mt-3 mb-4">
                <h1 class="text-3xl font-semibold tracking-tight text-[#23170f]">Forgot your password?</h1>
                <p class="mt-1 text-sm text-[#584637]">Enter your email and we will send a reset link so you can choose a new password.</p>
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

            <form method="POST" action="<?php echo e(route('password.email')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <p class="border-b border-[#d4c5b2] pb-2 text-[11px] font-semibold uppercase tracking-[0.09em] text-[#604c3a]">Password Reset</p>

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
                    Email Password Reset Link
                </button>

                <p class="pt-1 text-center text-sm text-[#6f5b46]">
                    Remembered your password?
                    <a href="<?php echo e(route('login')); ?>" class="font-medium text-[#4b3828] hover:underline">Log in</a>
                </p>
            </form>
        </section>
    </main>
</body>
</html>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/auth/forgot-password.blade.php ENDPATH**/ ?>