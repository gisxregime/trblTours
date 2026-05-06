<x-app-layout>
    <div class="min-h-screen bg-white py-8">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">
            <section class="rounded-2xl border border-[#d4a563]/35 bg-white p-6 shadow-[0_18px_36px_-22px_rgba(122,85,50,0.5)]">
                <h1 class="text-2xl font-bold text-[#7a5532]">Guide Settings</h1>
                <p class="mt-2 text-sm text-[#8a6746]">Manage your account security.</p>
            </section>

            <section class="rounded-2xl border border-[#d4a563]/35 bg-white p-6 shadow-[0_18px_36px_-22px_rgba(122,85,50,0.5)]">
                <h2 class="text-xl font-semibold text-[#7a5532]">Change Password</h2>

                <form method="POST" action="{{ route('dashboard.guide.settings.password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div x-data="{ show: false }">
                        <label for="current_password" class="text-sm font-medium text-[#7a5532]">Current Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="current_password" name="current_password" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="current-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <x-input-error :messages="$errors->guidePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="text-sm font-medium text-[#7a5532]">New Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="password" name="password" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="new-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <p class="mt-1 text-xs text-[#8a6746]">Minimum 8 characters, at least 1 uppercase letter, and 1 number.</p>
                        <x-input-error :messages="$errors->guidePassword->get('password')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="text-sm font-medium text-[#7a5532]">Confirm New Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="password_confirmation" name="password_confirmation" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="new-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <x-input-error :messages="$errors->guidePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="reset" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Save
                        </button>
                        @if (session('status') === 'guide-password-updated')
                            <p class="text-sm text-[#7a5532]">Password updated.</p>
                        @endif
                    </div>
                </form>
            </section>

            <section class="rounded-2xl border border-red-200 bg-white p-6 shadow-[0_18px_36px_-22px_rgba(122,85,50,0.5)]">
                <h2 class="text-xl font-semibold text-red-700">Delete Account</h2>
                <p class="mt-2 text-sm text-red-700/90">Delete your account and all associated data permanently.</p>

                <button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-guide-deletion')"
                    class="mt-4 inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                >
                    Delete Account
                </button>

                <x-modal name="confirm-guide-deletion" :show="$errors->guideDeletion->isNotEmpty()" focusable>
                    <form method="POST" action="{{ route('dashboard.guide.settings.destroy') }}" class="p-6" x-data="{ confirmDelete: {{ old('confirm_data_deletion') ? 'true' : 'false' }} }">
                        @csrf
                        @method('DELETE')

                        <h3 class="text-lg font-semibold text-[#7a5532]">Are you sure? This action is permanent and cannot be undone.</h3>
                        <p class="mt-2 text-sm text-[#8a6746]">Please confirm your password and acknowledge permanent deletion before continuing.</p>

                        <div class="mt-4">
                            <label for="delete_password" class="text-sm font-medium text-[#7a5532]">Password Confirmation</label>
                            <div style="position: relative;">
                                <input
                                    id="delete_password"
                                    name="password"
                                    type="password"
                                    style="padding-right: 40px;"
                                    class="mt-2 block w-full rounded-lg border border-[#d4a563]/45 px-3 py-2 text-sm text-[#5b3a26] focus:border-[#c69958] focus:ring-[#c69958]"
                                    required
                                    data-password-toggle-input="delete_password"
                                >
                                <button type="button" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; cursor: pointer; font-size: 12px; font-weight: 700; color: #5b3a26;" data-password-toggle="delete_password">Show</button>
                            </div>
                            <x-input-error :messages="$errors->guideDeletion->get('password')" class="mt-2" />
                        </div>

                        <label class="mt-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3">
                            <input
                                id="confirm_data_deletion"
                                name="confirm_data_deletion"
                                type="checkbox"
                                value="1"
                                x-model="confirmDelete"
                                class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500"
                            >
                            <span class="text-sm text-red-700">I understand this will delete all my data including booking history</span>
                        </label>
                        <x-input-error :messages="$errors->guideDeletion->get('confirm_data_deletion')" class="mt-2" />

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                x-bind:disabled="!confirmDelete"
                                x-bind:class="confirmDelete ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed'"
                                class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white transition"
                            >
                                Delete Account
                            </button>
                        </div>
                    </form>
    
                </x-modal>
                <hr class="my-6 h-0.5 border-t-0 bg-[#c69958]" />
                <!-- Back to Dashboard Button (always bottom) -->
                        <div class="flex justify-end pt-8">
                            <a href="{{ route('dashboard.guide.dashboard') }}" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
            </section>
        </div>
    </div>
</x-app-layout>
