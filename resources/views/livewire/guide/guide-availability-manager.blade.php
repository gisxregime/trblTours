<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
<div class="bg-gradient-to-b from-slate-50 via-white to-emerald-50 py-10">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-slate-900">Guide Availability</h2>
                    <p class="mt-1 text-sm text-slate-600">Set your daily availability and special pricing for peak dates.</p>
                </div>

                <a href="{{ route('dashboard.guide') }}" class="inline-flex items-center rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">
                    Back to Dashboard
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">{{ $editingId ? 'Edit Availability' : 'Add Availability' }}</h3>
                @if ($editingId)
                    <button type="button" wire:click="cancel" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                @endif
            </div>

            <form wire:submit.prevent="save" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="availabilityDate" class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                    <input id="availabilityDate" wire:model.live="form.date" type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-0">
                    @error('form.date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="availabilityStatus" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select id="availabilityStatus" wire:model.live="form.status" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-0">
                        <option value="available">Available</option>
                        <option value="limited_slots">Limited Slots</option>
                        <option value="fully_booked">Fully Booked</option>
                        <option value="fiesta">Fiesta Date</option>
                    </select>
                    @error('form.status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="specialPrice" class="mb-1 block text-sm font-medium text-slate-700">Special Price (optional)</label>
                    <input id="specialPrice" wire:model.live="form.special_price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-0">
                    @error('form.special_price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="availabilitySlots" class="mb-1 block text-sm font-medium text-slate-700">Slots (required for Limited Slots)</label>
                    <input id="availabilitySlots" wire:model.live="form.slots" type="number" min="1" max="200" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-0" placeholder="e.g. 6">
                    @error('form.slots') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="availabilityNote" class="mb-1 block text-sm font-medium text-slate-700">Note</label>
                    <textarea id="availabilityNote" wire:model.live="form.note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-0"></textarea>
                    @error('form.note') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="rounded-lg bg-[#7a8730] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#697629]">
                        {{ $editingId ? 'Update Availability' : 'Save Availability' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Upcoming Availability</h3>
                <span class="text-sm text-slate-500">{{ $rows->count() }} entries</span>
            </div>

            @if ($rows->isEmpty())
                <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">No availability entries yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($rows as $row)
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-base font-semibold text-slate-900">{{ $row->date?->format('M d, Y') ?? $row->date }}</p>
                                    <p class="mt-1 text-sm text-slate-600 capitalize">{{ str_replace('_', ' ', $row->status) }}</p>
                                    @if ($row->slots !== null)
                                        <p class="mt-1 text-sm text-slate-600">Slots: {{ (int) $row->slots }}</p>
                                    @endif
                                    @if ($row->special_price !== null)
                                        <p class="mt-1 text-sm text-slate-600">Special Price: PHP {{ number_format((float) $row->special_price, 2) }}</p>
                                    @endif
                                    @if ($row->note)
                                        <p class="mt-1 text-sm text-slate-700">{{ $row->note }}</p>
                                    @endif
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" wire:click="edit({{ $row->id }})" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                                        Edit
                                    </button>
                                    <button type="button" wire:click="delete({{ $row->id }})" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
