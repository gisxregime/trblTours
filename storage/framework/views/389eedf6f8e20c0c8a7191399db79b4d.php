<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrblTours - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --sand-100: #f5efe2;
            --sand-200: #efe5d4;
            --sand-300: #e5d6be;
            --brown-700: #6f5d52;
            --brown-900: #3f2d22;
            --olive: #5f8a46;
            --olive-soft: #eaf3de;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-lg: 0 24px 50px -30px rgba(46, 31, 23, 0.55);
        }

        body {
            background: radial-gradient(circle at 15% 15%, #fbf8f1 0%, var(--sand-100) 42%, #ebe0cc 100%);
            color: var(--brown-900);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .profile-shell {
            width: min(980px, calc(100% - 2rem));
            margin: 2.25rem auto;
            border: 1px solid #e2d3b6;
            border-radius: var(--radius-xl);
            background: rgba(255, 251, 243, 0.94);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            backdrop-filter: blur(8px);
        }

        .profile-cover {
            position: relative;
            min-height: 210px;
            background: #5f8a46;
            background-size: cover;
            background-position: center;
        }

        .profile-cover::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(38, 27, 20, 0.2) 0%, rgba(38, 27, 20, 0.65) 100%);
        }

        .profile-top-actions {
            position: absolute;
            z-index: 2;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem;
        }

        .pill-link {
            border: 1px solid #ffffff66;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff8eb;
            font-size: 0.83rem;
            font-weight: 800;
            padding: 0.42rem 0.85rem;
            transition: all 0.2s ease;
        }

        .pill-link:hover {
            background: rgba(123, 167, 100, 0.22);
        }

        .profile-main {
            display: grid;
            gap: 1.25rem;
            margin-top: -72px;
            padding: 0 1.25rem 1.25rem;
            position: relative;
            z-index: 3;
        }

        .identity-card,
        .form-card {
            background: #fffbf4;
            border: 1px solid #ebdcc0;
            border-radius: var(--radius-lg);
        }

        .identity-card {
            display: grid;
            gap: 0.85rem;
            padding: 1rem;
        }

        .avatar-wrap {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }

        .avatar {
            width: 108px;
            height: 108px;
            border-radius: 999px;
            border: 4px solid #fff9ee;
            box-shadow: 0 14px 26px -18px rgba(35, 26, 19, 0.8);
            background: linear-gradient(140deg, #c9ac7f 0%, #9f7d56 100%);
            background-size: cover;
            background-position: center;
            color: #fffaf1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 2.05rem;
            font-weight: 700;
        }

        .upload-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid #d4c0a0;
            background: #fff6e8;
            color: #594636;
            font-size: 0.79rem;
            font-weight: 700;
            padding: 0.46rem 0.92rem;
            transition: all 0.16s ease;
            cursor: pointer;
        }

        .upload-label:hover {
            border-color: #b49362;
            background: #fff2de;
        }

        .upload-label.is-disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: not-allowed;
        }

        .upload-label.is-hidden {
            display: none;
        }

        .identity-name {
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 800;
            color: #332a1b;
            font-size: 28px;
            line-height: 1.2;
            position: relative;
            z-index: 2;
            padding: 0 1.2rem 1rem;
        }

        .identity-meta {
            color: var(--brown-700);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .form-card {
            padding: 1rem;
        }

        .form-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .form-title {
            font-size: 1.23rem;
            font-weight: 700;
            color: var(--brown-900);
        }

        .form-subtitle {
            color: #786654;
            font-size: 0.88rem;
            margin-top: 0.15rem;
        }

        .edit-trigger,
        .save-btn,
        .cancel-btn {
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
            padding: 0.55rem 1.04rem;
            transition: all 0.17s ease;
        }

        .edit-trigger {
            border: 1px solid #d3bc98;
            background: #fff5e4;
            color: #594635;
        }

        .edit-trigger:hover {
            border-color: #b28f5f;
            background: #ffefd8;
        }

        .save-btn {
            border: 1px solid #4f7740;
            background: var(--olive);
            color: #f7fff4;
        }

        .save-btn:hover {
            background: #4f7740;
        }

        .cancel-btn {
            border: 1px solid transparent;
            background: transparent;
            color: #6f5d52;
        }

        .cancel-btn:hover {
            background: #f4e9d7;
        }

        .fields {
            display: grid;
            gap: 0.95rem;
            margin-top: 1rem;
        }

        .field-grid {
            display: grid;
            gap: 0.95rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field {
            display: grid;
            gap: 0.42rem;
        }

        .field-label {
            color: #6e5c4c;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .field-input,
        .field-textarea {
            width: 100%;
            border-radius: 14px;
            border: 1px solid #dccab0;
            background: #fffdf8;
            color: #4d3d30;
            font-size: 0.94rem;
            padding: 0.69rem 0.82rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .field-input:focus,
        .field-textarea:focus {
            border-color: #b89466;
            box-shadow: 0 0 0 3px rgba(184, 148, 102, 0.2);
            outline: none;
        }

        .field-input:disabled,
        .field-textarea:disabled {
            background: #f5ecdd;
            color: #7f6d5a;
            cursor: not-allowed;
        }

        .field-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .save-group {
            display: none;
            justify-content: flex-end;
            gap: 0.45rem;
            margin-top: 1.08rem;
        }

        .save-group.is-visible {
            display: flex;
        }

        .success-flash {
            border-radius: 12px;
            border: 1px solid #c8ddb9;
            background: var(--olive-soft);
            color: #3f6234;
            padding: 0.58rem 0.7rem;
            font-size: 0.84rem;
            font-weight: 600;
            margin-top: 0.85rem;
        }

        .error-text {
            color: #a83828;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .error-flash {
            border-radius: 12px;
            border: 1px solid #edc5bd;
            background: #fff1ee;
            color: #9a2f22;
            padding: 0.58rem 0.7rem;
            font-size: 0.84rem;
            font-weight: 600;
            margin-top: 0.85rem;
        }

        @media (max-width: 768px) {
            .profile-shell {
                margin: 1rem auto;
                width: calc(100% - 1rem);
            }

            .profile-main {
                margin-top: -56px;
                padding: 0 0.7rem 0.8rem;
            }

            .identity-name {
                margin-top: 1.8rem;
                padding-left: 0.8rem;
                padding-right: 0.8rem;
            }

            .field-grid {
                grid-template-columns: 1fr;
            }

            .form-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .save-group.is-visible {
                flex-direction: column-reverse;
            }

            .save-btn,
            .cancel-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <?php
        $dashboardRouteName = auth()->user()->dashboardRouteName();
        $dashboardUrl = \Illuminate\Support\Facades\Route::has($dashboardRouteName)
            ? route($dashboardRouteName)
            : route('dashboard.tourist');

        $displayName = old('full_name', $user->full_name ?: $user->name);
        $initial = strtoupper(substr(trim($displayName), 0, 1) ?: 'T');
        $profilePhotoUrl = $user->profile_photo_path
            ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path)
            : null;
        $coverPhotoUrl = $user->cover_photo_path
            ? \Illuminate\Support\Facades\Storage::url($user->cover_photo_path)
            : null;
    ?>

    <div class="profile-shell" data-profile-editor data-start-editing="<?php echo e(count($errors) > 0 ? '1' : '0'); ?>">
        <div class="profile-cover" id="coverPreview"
            <?php if($coverPhotoUrl): ?> style="background-image: url('<?php echo e($coverPhotoUrl); ?>');" <?php endif; ?>>
            <div class="profile-top-actions">
                <a href="<?php echo e($dashboardUrl); ?>" class="pill-link">&larr; Back</a>
            </div>
        </div>

        <div class="profile-main">
            <section class="identity-card">
                <div>
                    <h2 class="form-title">Profile Details</h2>
                </div>
                <div class="avatar-wrap">
                    <div class="avatar" id="profilePreview"
                        <?php if($profilePhotoUrl): ?> style="background-image: url('<?php echo e($profilePhotoUrl); ?>');" <?php endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$profilePhotoUrl): ?>
                            <span id="profileInitial"><?php echo e($initial); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <label for="profile_photo" class="upload-label is-disabled is-hidden"
                            data-upload-label="profile_photo">Change Profile Photo</label>
                        <label for="cover_photo" class="upload-label is-disabled is-hidden" data-upload-label="cover_photo">Change
                            Cover Photo</label>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profile_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="error-text"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cover_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="error-text"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <h1 class="identity-name" id="identityName"><?php echo e($displayName); ?></h1>
            </section>

            <section class="form-card">
                <form method="post" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data"
                    data-profile-form>
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('patch'); ?>

                    <input id="email" name="email" type="hidden" value="<?php echo e($user->email); ?>">

                    <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*"
                        data-editable="true" disabled>
                    <input id="cover_photo" name="cover_photo" type="file" class="hidden" accept="image/*"
                        data-editable="true" disabled>

                    <div class="form-head">
                        <div>
                            <p class="identity-meta">Update your traveler details and keep your profile fresh for guides and future bookings.</p>
                        </div>

                        <button type="button" class="edit-trigger" data-edit-toggle>Edit</button>
                    </div>

                    <div class="fields">
                        <div class="field-grid">
                            <div class="field">
                                <label class="field-label" for="full_name">Full Name</label>
                                <input id="full_name" name="full_name" type="text"
                                    value="<?php echo e(old('full_name', $user->full_name ?: $user->name)); ?>" class="field-input"
                                    data-editable="true" disabled required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-text"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label" for="bio">Bio / Short Description</label>
                            <textarea id="bio" name="bio" class="field-textarea" data-editable="true" disabled
                                placeholder="Share your favorite trips, interests, or travel style."><?php echo e(old('bio', $user->bio)); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="error-text"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field-label" for="region">Location</label>
                            <input id="region" name="region" type="text"
                                value="<?php echo e(old('region', $user->region)); ?>" class="field-input" data-editable="true"
                                disabled placeholder="City, province, or region">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['region'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="error-text"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="save-group" data-save-actions>
                        <button type="button" class="cancel-btn" data-cancel-edit>Cancel</button>
                        <button type="submit" class="save-btn">Save Changes</button>
                    </div>

                    <div class="error-flash" data-save-error hidden></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') === 'profile-updated'): ?>
                        <div class="success-flash">Profile updated successfully.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>
            </section>
        </div>
    </div>

    <script>
        (function() {
            const root = document.querySelector('[data-profile-editor]');

            if (!root) {
                return;
            }

            const form = root.querySelector('[data-profile-form]');
            const toggle = root.querySelector('[data-edit-toggle]');
            const cancel = root.querySelector('[data-cancel-edit]');
            const editableFields = root.querySelectorAll('[data-editable="true"]');
            const uploadLabels = root.querySelectorAll('[data-upload-label]');
            const saveActions = root.querySelector('[data-save-actions]');
            const nameInput = root.querySelector('#full_name');
            const identityName = root.querySelector('#identityName');
            const profileInput = root.querySelector('#profile_photo');
            const coverInput = root.querySelector('#cover_photo');
            const profilePreview = root.querySelector('#profilePreview');
            const coverPreview = root.querySelector('#coverPreview');
            const shouldStartEditing = root.dataset.startEditing === '1';
            const saveButton = root.querySelector('.save-btn');
            const saveError = root.querySelector('[data-save-error]');

            let isEditing = false;
            let isSaving = false;
            let profileObjectUrl = null;
            let coverObjectUrl = null;

            const toggleSaveError = (message = null) => {
                if (!saveError) {
                    return;
                }

                if (!message) {
                    saveError.hidden = true;
                    saveError.textContent = '';
                    return;
                }

                saveError.hidden = false;
                saveError.textContent = message;
            };

            const resetTemporaryPreviewState = () => {
                if (profileObjectUrl) {
                    URL.revokeObjectURL(profileObjectUrl);
                    profileObjectUrl = null;
                }

                if (coverObjectUrl) {
                    URL.revokeObjectURL(coverObjectUrl);
                    coverObjectUrl = null;
                }

                profileInput.value = '';
                coverInput.value = '';
            };

            const setSavingState = (value) => {
                isSaving = value;

                if (saveButton) {
                    saveButton.disabled = isSaving;
                    saveButton.textContent = isSaving ? 'Saving...' : 'Save Changes';
                }

                if (toggle) {
                    toggle.disabled = isSaving;
                }

                if (cancel) {
                    cancel.disabled = isSaving;
                }

                uploadLabels.forEach((label) => {
                    label.classList.toggle('is-disabled', isSaving || !isEditing);
                });
            };

            const applyServerState = (payload) => {
                const profileData = payload?.data ?? {};

                if (nameInput && profileData.display_name) {
                    nameInput.value = profileData.display_name;
                }

                if (identityName && profileData.display_name) {
                    identityName.textContent = profileData.display_name;
                }

                if (profileData.bio !== undefined) {
                    const bioInput = root.querySelector('#bio');

                    if (bioInput) {
                        bioInput.value = profileData.bio ?? '';
                    }
                }

                if (profileData.region !== undefined) {
                    const regionInput = root.querySelector('#region');

                    if (regionInput) {
                        regionInput.value = profileData.region ?? '';
                    }
                }

                if (profilePreview) {
                    if (profileData.profile_photo_url) {
                        profilePreview.style.backgroundImage = `url(${profileData.profile_photo_url})`;
                        const initial = root.querySelector('#profileInitial');

                        if (initial) {
                            initial.remove();
                        }
                    } else {
                        profilePreview.style.backgroundImage = '';
                        let initial = root.querySelector('#profileInitial');

                        if (!initial) {
                            initial = document.createElement('span');
                            initial.id = 'profileInitial';
                            profilePreview.appendChild(initial);
                        }

                        initial.textContent = profileData.initial || 'T';
                    }
                }

                if (coverPreview) {
                    coverPreview.style.backgroundImage = profileData.cover_photo_url
                        ? `url(${profileData.cover_photo_url})`
                        : '';
                }
            };

            const setEditMode = (value) => {
                isEditing = value;

                editableFields.forEach((element) => {
                    element.disabled = !isEditing;
                });

                uploadLabels.forEach((label) => {
                    label.classList.toggle('is-disabled', !isEditing);
                    label.classList.toggle('is-hidden', !isEditing);
                });

                saveActions.classList.toggle('is-visible', isEditing);
                toggle.textContent = isEditing ? 'Editing...' : 'Edit';
            };

            toggle.addEventListener('click', () => {
                if (isEditing || isSaving) {
                    return;
                }

                toggleSaveError();
                setEditMode(true);
            });

            cancel.addEventListener('click', () => {
                if (isSaving) {
                    return;
                }

                window.location.reload();
            });

            nameInput.addEventListener('input', () => {
                const nextName = nameInput.value.trim();

                if (identityName) {
                    identityName.textContent = nextName || 'Tourist';
                }

                if (!isEditing) {
                    saveActions.classList.add('is-visible');
                }
            });

            profileInput.addEventListener('change', () => {
                const file = profileInput.files?.[0];

                if (!file) {
                    return;
                }

                if (profileObjectUrl) {
                    URL.revokeObjectURL(profileObjectUrl);
                }

                profileObjectUrl = URL.createObjectURL(file);
                profilePreview.style.backgroundImage = `url(${profileObjectUrl})`;
                const initial = root.querySelector('#profileInitial');

                if (initial) {
                    initial.remove();
                }
            });

            coverInput.addEventListener('change', () => {
                const file = coverInput.files?.[0];

                if (!file) {
                    return;
                }

                if (coverObjectUrl) {
                    URL.revokeObjectURL(coverObjectUrl);
                }

                coverObjectUrl = URL.createObjectURL(file);
                coverPreview.style.backgroundImage = `url(${coverObjectUrl})`;
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (isSaving) {
                    return;
                }

                toggleSaveError();

                // Ensure all editable controls (including file inputs) are submitted.
                editableFields.forEach((element) => {
                    element.disabled = false;
                });

                setSavingState(true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    const payload = await response.json().catch(() => null);

                    if (!response.ok) {
                        if (response.status === 422) {
                            const firstValidationError = payload?.errors
                                ? Object.values(payload.errors).flat()[0]
                                : null;

                            throw new Error(firstValidationError || 'Please check your profile details and try again.');
                        }

                        throw new Error(payload?.message || 'Unable to save profile right now.');
                    }

                    applyServerState(payload);
                    resetTemporaryPreviewState();
                    setEditMode(false);
                } catch (error) {
                    const message = error instanceof Error
                        ? error.message
                        : 'Unable to save profile right now.';

                    toggleSaveError(message);
                    setEditMode(true);
                } finally {
                    setSavingState(false);
                }

                editableFields.forEach((element) => {
                    element.disabled = !isEditing;
                });
            });

            if (shouldStartEditing) {
                setEditMode(true);
            }
        })();
    </script>
</body>

</html>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/profile/edit.blade.php ENDPATH**/ ?>