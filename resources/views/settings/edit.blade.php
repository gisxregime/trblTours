<x-app-layout>
    @php
        $dashboardRouteName = auth()->user()->dashboardRouteName();
        $dashboardUrl = \Illuminate\Support\Facades\Route::has($dashboardRouteName)
            ? route($dashboardRouteName)
            : route('dashboard.tourist');
    @endphp

    <div class="relative left-1/2 right-1/2 w-screen -translate-x-1/2 min-h-screen bg-[#f5efe2] py-10">
        <div class="mx-auto w-full max-w-[1260px] space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-6 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <div>
                         <a href="{{ $dashboardUrl }}" class="inline-flex items-center rounded-full border border-[#d8c7a7] bg-[#fff3df] px-4 py-2 text-sm font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                        <span aria-hidden="true" class="mr-1">&larr;</span>
                        Back
                    </a>
                        <h1 class="mt-4 text-2xl font-bold text-[#3f2d22]">Account Settings</h1>
                        <p class="mt-1 text-sm text-[#6f5d52]">Update your email and password in one place.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-6 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    @if (session('status') === 'settings-updated')
                        <div class="rounded-xl border border-[#bfd4aa] bg-[#edf8e1] px-4 py-3 text-sm font-medium text-[#3f6234]">
                            Settings updated successfully.
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold text-[#3f2d22]">Update Email</h2>
                            <p class="text-sm text-[#6f5d52]">Use an email address that you can access.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="text-xs font-bold uppercase tracking-[0.09em] text-[#6f5d52]">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                autocomplete="email"
                                class="block w-full rounded-xl border border-[#dcc9ad] bg-[#fffdf8] px-3.5 py-2.5 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                            >
                            @error('email')
                                <p class="text-sm font-medium text-[#a83828]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-[#eadcc3] pt-6">
                        <div>
                            <h2 class="text-lg font-semibold text-[#3f2d22]">Change Password</h2>
                            <p class="text-sm text-[#6f5d52]">Leave blank if you do not want to change your password.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div x-data="{ showCurrentPassword: false }" class="space-y-1.5 md:col-span-2">
                                <label for="current_password" class="text-xs font-bold uppercase tracking-[0.09em] text-[#6f5d52]">Current Password</label>
                                <div class="relative">
                                    <input
                                        id="current_password"
                                        name="current_password"
                                        x-bind:type="showCurrentPassword ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        class="block w-full rounded-xl border border-[#dcc9ad] bg-[#fffdf8] px-3.5 py-2.5 pr-16 text-sm text-[#4d3d30] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="showCurrentPassword = !showCurrentPassword"
                                        class="absolute inset-y-0 right-3 text-xs font-semibold text-[#6f5d52] transition hover:text-[#3f2d22]"
                                        x-text="showCurrentPassword ? 'Hide' : 'Show'"
                                    ></button>
                                </div>
                                @error('current_password')
                                    <p class="text-sm font-medium text-[#a83828]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ showNewPassword: false }" class="space-y-1.5">
                                <label for="new_password" class="text-xs font-bold uppercase tracking-[0.09em] text-[#6f5d52]">New Password</label>
                                <div class="relative">
                                    <input
                                        id="new_password"
                                        name="new_password"
                                        x-bind:type="showNewPassword ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        class="block w-full rounded-xl border border-[#dcc9ad] bg-[#fffdf8] px-3.5 py-2.5 pr-16 text-sm text-[#4d3d30] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-0 right-3 text-xs font-semibold text-[#6f5d52] transition hover:text-[#3f2d22]"
                                        x-text="showNewPassword ? 'Hide' : 'Show'"
                                    ></button>
                                </div>
                                @error('new_password')
                                    <p class="text-sm font-medium text-[#a83828]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ showConfirmPassword: false }" class="space-y-1.5">
                                <label for="new_password_confirmation" class="text-xs font-bold uppercase tracking-[0.09em] text-[#6f5d52]">Confirm New Password</label>
                                <div class="relative">
                                    <input
                                        id="new_password_confirmation"
                                        name="new_password_confirmation"
                                        x-bind:type="showConfirmPassword ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        class="block w-full rounded-xl border border-[#dcc9ad] bg-[#fffdf8] px-3.5 py-2.5 pr-16 text-sm text-[#4d3d30] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-3 text-xs font-semibold text-[#6f5d52] transition hover:text-[#3f2d22]"
                                        x-text="showConfirmPassword ? 'Hide' : 'Show'"
                                    ></button>
                                </div>
                                @error('new_password_confirmation')
                                    <p class="text-sm font-medium text-[#a83828]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[#eadcc3] pt-6">
                        <a href="{{ $dashboardUrl }}" class="inline-flex items-center rounded-full border border-[#d8c7a7] bg-transparent px-5 py-2.5 text-sm font-semibold text-[#6f5d52] transition hover:bg-[#f3e8d4]">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-5 py-2.5 text-sm font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
