<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-900">
            {{ __('Edit Guide Profile') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">

    <div class="bg-white py-4">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold text-slate-900">Guide Profile Details</h3>
                    <p class="mt-1 text-sm text-slate-600">Update your public profile and keep your guide information accurate.</p>
                </div>

                <form id="guideProfileForm" action="{{ route('dashboard.guide.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="full_name" class="mb-1 block text-sm font-medium text-slate-700">Full Name</label>
                            <input id="full_name" name="full_name" type="text" required value="{{ $form['full_name'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label for="display_name" class="mb-1 block text-sm font-medium text-slate-700">Display Name</label>
                            <input id="display_name" name="display_name" type="text" required value="{{ $form['display_name'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label for="phone_number" class="mb-1 block text-sm font-medium text-slate-700">Phone Number</label>
                            <input id="phone_number" name="phone_number" type="text" required placeholder="09XX-XXX-XXXX" value="{{ $form['phone_number'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label for="date_of_birth" class="mb-1 block text-sm font-medium text-slate-700">Date of Birth</label>
                            <input id="date_of_birth" name="date_of_birth" type="date" required value="{{ $form['date_of_birth'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label for="region" class="mb-1 block text-sm font-medium text-slate-700">Region</label>
                            <select id="region" name="region" required class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                                <option value="">Select a region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}" @selected($form['region'] === $region)>{{ $region }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="city_municipality" class="mb-1 block text-sm font-medium text-slate-700">City/Municipality</label>
                            <input id="city_municipality" name="city_municipality" type="text" required value="{{ $form['city_municipality'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label for="barangay" class="mb-1 block text-sm font-medium text-slate-700">Barangay</label>
                            <input id="barangay" name="barangay" type="text" required value="{{ $form['barangay'] }}" class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">
                        </div>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <label for="bio" class="block text-sm font-medium text-slate-700">Bio</label>
                            <span id="bioCounter" class="text-xs font-medium text-slate-500">0 / 1000</span>
                        </div>
                        <textarea id="bio" name="bio" rows="6" minlength="100" maxlength="1000" required class="w-full rounded-lg border border-[#d4a563]/55 px-3 py-2.5 text-sm text-slate-900 focus:border-[#d4a563] focus:outline-none focus:ring-0">{{ $form['bio'] }}</textarea>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <label for="profile_photo" class="mb-2 block text-sm font-medium text-slate-700">Profile Photo</label>
                            <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-[#7a8730] file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-[#697629]">
                            <p class="mt-2 text-xs text-slate-500">Optional. Upload to replace your current profile photo.</p>

                            <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white p-2">
                                <img id="profilePreview" src="{{ $form['profile_photo_path'] !== '' ? asset('storage/'.$form['profile_photo_path']) : '' }}" alt="Profile preview" class="h-56 w-full object-contain {{ $form['profile_photo_path'] === '' ? 'hidden' : '' }}">
                                <p id="profilePreviewEmpty" class="{{ $form['profile_photo_path'] === '' ? '' : 'hidden' }} py-16 text-center text-xs text-slate-500">No profile photo selected yet.</p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <label for="cover_photo" class="mb-2 block text-sm font-medium text-slate-700">Cover Photo</label>
                            <input id="cover_photo" name="cover_photo" type="file" accept="image/*" class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-[#7a8730] file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-[#697629]">
                            <p class="mt-2 text-xs text-slate-500">Optional. Recommended size: 1200x400.</p>

                            <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white p-2">
                                <img id="coverPreview" src="{{ $form['cover_photo_path'] !== '' ? asset('storage/'.$form['cover_photo_path']) : '' }}" alt="Cover preview" class="h-56 w-full object-contain {{ $form['cover_photo_path'] === '' ? 'hidden' : '' }}">
                                <p id="coverPreviewEmpty" class="{{ $form['cover_photo_path'] === '' ? '' : 'hidden' }} py-16 text-center text-xs text-slate-500">No cover photo selected yet.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-semibold text-slate-800">Profile Photo Cropper</h4>
                        <p class="mt-1 text-xs text-slate-500">Adjust your profile photo crop before submitting.</p>
                        <div class="mt-3 overflow-hidden rounded-lg border border-slate-200 bg-white p-2">
                            <img id="cropperTarget" alt="Cropper target" class="hidden max-h-72 w-full object-contain">
                            <p id="cropperHint" class="py-16 text-center text-xs text-slate-500">Upload a profile photo to enable cropping.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard.guide.profile.show') }}" class="inline-flex items-center rounded-lg border border-[#d4a563]/70 bg-white px-5 py-2.5 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">
                            Cancel
                        </a>
                        <button id="saveProfileButton" type="submit" class="rounded-lg bg-[#d4a563] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c69958]">
                            Save Profile
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div id="toastContainer" class="pointer-events-none fixed right-4 top-4 z-[100] flex w-full max-w-sm flex-col gap-2"></div>

    <!-- Back to Dashboard button at the very bottom -->
    <div class="flex justify-end mt-10">
        <a href="{{ route('dashboard.guide.dashboard') }}" class="inline-flex items-center rounded-lg border border-[#d4a563]/45 bg-[#d4a563] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#bf9155]">
            Back to Dashboard
        </a>
    </div>

    <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        (() => {
            const form = document.getElementById('guideProfileForm');
            const bio = document.getElementById('bio');
            const bioCounter = document.getElementById('bioCounter');
            const profileInput = document.getElementById('profile_photo');
            const coverInput = document.getElementById('cover_photo');
            const profilePreview = document.getElementById('profilePreview');
            const profilePreviewEmpty = document.getElementById('profilePreviewEmpty');
            const coverPreview = document.getElementById('coverPreview');
            const coverPreviewEmpty = document.getElementById('coverPreviewEmpty');
            const cropperTarget = document.getElementById('cropperTarget');
            const cropperHint = document.getElementById('cropperHint');
            const saveButton = document.getElementById('saveProfileButton');
            const toastContainer = document.getElementById('toastContainer');

            let cropper = null;

            const showToast = (message, type = 'success') => {
                const toast = document.createElement('div');
                toast.className = `pointer-events-auto rounded-lg border px-4 py-3 text-sm shadow-lg ${type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800'}`;
                toast.textContent = message;
                toastContainer.appendChild(toast);

                window.setTimeout(() => {
                    toast.remove();
                }, 4000);
            };

            const updateBioCounter = () => {
                bioCounter.textContent = `${bio.value.length} / 1000`;
            };

            const readFileAsDataURL = (file) => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (event) => resolve(event.target?.result ?? '');
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });

            const setImagePreview = async (input, img, empty, crop = false) => {
                const file = input.files[0];
                if (!file) {
                    return;
                }

                const dataUrl = await readFileAsDataURL(file);
                img.src = dataUrl;
                img.classList.remove('hidden');
                empty.classList.add('hidden');

                if (!crop) {
                    return;
                }

                cropperHint.classList.add('hidden');
                cropperTarget.classList.remove('hidden');
                cropperTarget.src = dataUrl;

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropperTarget, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                });
            };

            profileInput.addEventListener('change', async () => {
                await setImagePreview(profileInput, profilePreview, profilePreviewEmpty, true);
            });

            coverInput.addEventListener('change', async () => {
                await setImagePreview(coverInput, coverPreview, coverPreviewEmpty, false);
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';

                const formData = new FormData(form);

                if (cropper && profileInput.files[0]) {
                    const blob = await new Promise((resolve) => {
                        cropper.getCroppedCanvas({
                            width: 600,
                            height: 600,
                            imageSmoothingQuality: 'high',
                        }).toBlob(resolve, profileInput.files[0].type || 'image/jpeg', 0.92);
                    });

                    if (blob) {
                        formData.set('profile_photo', blob, profileInput.files[0].name || 'profile-photo.jpg');
                    }
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token'),
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    const firstError = data?.errors ? Object.values(data.errors)[0][0] : (data?.message || 'Unable to save profile.');
                    showToast(firstError, 'error');
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Profile';
                    return;
                }

                showToast(data.message || 'Profile updated successfully.', 'success');
                saveButton.disabled = false;
                saveButton.textContent = 'Save Profile';

                const redirectTo = data?.redirect_to || '{{ route('dashboard.guide.profile.show') }}';
                window.setTimeout(() => {
                    window.location.assign(redirectTo);
                }, 500);
            });

            updateBioCounter();
            bio.addEventListener('input', updateBioCounter);
        })();
    </script>
</x-app-layout>
