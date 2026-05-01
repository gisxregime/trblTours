<div class="bg-white pb-10">
    <section class="mx-auto w-full max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-2xl border border-[#d4a563]/35 bg-white px-6 py-4 shadow-[0_10px_24px_-16px_rgba(122,85,50,0.55)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-slate-900">{{ $title }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Design your tour listing with a clean marketplace card preview.</p>
                </div>

                <a href="{{ route('dashboard.guide') }}" class="inline-flex items-center rounded-lg border border-[#d4a563]/45 bg-[#fff7ec] px-4 py-2 text-sm font-semibold text-[#7a5532] transition hover:bg-[#f7ead7]">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.75fr)_minmax(320px,1fr)]">
            <article class="rounded-2xl border border-[#d4a563]/30 bg-white p-6 shadow-[0_16px_34px_-20px_rgba(122,85,50,0.55)] sm:p-8">
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $editingTourId ? 'Edit Tour' : 'Create Tour' }}</h3>
                        <p class="text-sm text-slate-500">Fill in the details travelers need before booking.</p>
                    </div>

                </div>

                <form wire:submit.prevent="save" wire:key="tour-form-{{ $editingTourId ?? 'new' }}" class="space-y-6">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700" for="tourTitle">Tour Title</label>
                        <input id="tourTitle" wire:model.live="form.title" type="text" placeholder="Enter Tour Title" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                        @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700" for="region">Region</label>
                            <input id="region" wire:model.live="form.region" type="text" placeholder="Palawan" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                            @error('form.region') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700" for="city">City / Municipality</label>
                            <input id="city" wire:model.live="form.city" type="text" placeholder="Puerto Princesa" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                        </div>
                    </div>

                    <section class="space-y-3 rounded-2xl border border-[#d4a563]/25 bg-[#fffaf2] p-4 sm:p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Duration</h4>
                        <div class="grid gap-4 sm:grid-cols-[1fr_minmax(140px,180px)]">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="duration_hours">Length</label>
                                <input id="duration_hours" wire:model.live="form.duration_hours" type="number" min="1" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                @error('form.duration_hours') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="duration_unit">Unit</label>
                                <select id="duration_unit" wire:model.live="form.duration_unit" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 pr-10 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                    <option value="hours">Hours</option>
                                    <option value="days">Days</option>
                                </select>
                                @error('form.duration_unit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3 rounded-2xl border border-[#d4a563]/25 bg-[#fffaf2] p-4 sm:p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Transportation Options</h4>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($transportationOptions as $option)
                                @php
                                    $selectedTransportation = is_array($form['transportation'] ?? null) ? $form['transportation'] : [];
                                    $selected = in_array($option, $selectedTransportation, true);
                                @endphp
                                <button
                                    type="button"
                                    wire:click="toggleTransportation('{{ $option }}')"
                                    class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $selected ? 'border-[#7a8730] bg-[#7a8730] text-white shadow-[0_10px_24px_-14px_rgba(122,135,48,0.85)]' : 'border-[#d4a563]/35 bg-white text-[#7a5532] hover:border-[#c69958]' }}"
                                >
                                    {{ str_replace('_', ' ', ucwords($option, '_')) }}
                                </button>
                            @endforeach
                        </div>
                        @error('form.transportation') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </section>

                    <section class="space-y-3 rounded-2xl border border-[#d4a563]/25 bg-[#fffaf2] p-4 sm:p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Group Size</h4>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="min_guests">Minimum Guests</label>
                                <input id="min_guests" wire:model.live="form.min_guests" type="number" min="1" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                @error('form.min_guests') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="max_guests">Maximum Guests</label>
                                <input id="max_guests" wire:model.live="form.max_guests" type="number" min="1" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                @error('form.max_guests') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3 rounded-2xl border border-[#d4a563]/25 bg-[#fffaf2] p-4 sm:p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Price</h4>
                        <div class="grid gap-4 md:grid-cols-[minmax(96px,130px)_1fr_minmax(110px,140px)]">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="currency">Currency</label>
                                <select id="currency" wire:model.live="form.currency" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 pr-10 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                    <option value="PHP">PHP</option>
                                </select>
                                @error('form.currency') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="price_per_person">Amount</label>
                                <input id="price_per_person" wire:model.live="form.price_per_person" type="number" step="0.01" min="0" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                @error('form.price_per_person') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700" for="price_unit">Per</label>
                                <select id="price_unit" wire:model.live="form.price_unit" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 pr-10 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                    <option value="person">Person</option>
                                    <option value="group">Group</option>
                                </select>
                                @error('form.price_unit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3 rounded-2xl border border-[#d4a563]/25 bg-[#fffaf2] p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-sm font-semibold text-slate-800">Tour Package Pictures</h4>
                            <span class="text-xs text-slate-500">Max 5 photos</span>
                        </div>

                        <input
                            id="tour_photos"
                            wire:model="tourPhotos"
                            type="file"
                            accept="image/*"
                            multiple
                            class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#7a8730] file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-[#697629]"
                        >
                        <p class="text-xs text-slate-500">Upload up to 5 images for activity/tour preview. Photos are saved automatically after selection.</p>

                        <div wire:loading wire:target="tourPhotos" class="text-xs text-[#6c792a]">Uploading photos...</div>
                        @error('tourPhotos') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        @error('tourPhotos.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                        @php
                            $hasNewPhotos = is_array($tourPhotos ?? null) && count($tourPhotos) > 0;
                        @endphp
                        <div class="grid grid-cols-3 gap-2">
                            @if ($hasNewPhotos)
                                @foreach ($tourPhotos as $index => $photo)
                                    <div class="relative aspect-square overflow-hidden rounded-xl border border-[#d4a563]/30 bg-white">
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Tour photo preview" class="h-full w-full object-cover">
                                        <button
                                            type="button"
                                            wire:click="removeTourPhoto({{ $index }})"
                                            class="absolute right-1.5 top-1.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white transition hover:bg-black"
                                            aria-label="Remove photo"
                                        >
                                            <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @elseif ($existingGalleryImages !== [])
                                @foreach ($existingGalleryImages as $index => $photoPath)
                                    <div class="relative aspect-square overflow-hidden rounded-xl border border-[#d4a563]/30 bg-white">
                                        <img src="{{ str_starts_with($photoPath, 'data:image/') ? $photoPath : asset('storage/'.$photoPath) }}" alt="Saved tour photo" class="h-full w-full object-cover">
                                        <button
                                            type="button"
                                            wire:click="removeTourPhoto({{ $index }})"
                                            class="absolute right-1.5 top-1.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white transition hover:bg-black"
                                            aria-label="Remove photo"
                                        >
                                            <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-3 rounded-xl border border-dashed border-[#d4a563]/40 bg-white px-4 py-5 text-center text-xs text-slate-500">
                                    No photos uploaded yet.
                                </div>
                            @endif
                        </div>
                    </section>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700" for="summary">Tour Summary</label>
                        <textarea id="summary" wire:model.live="form.summary" rows="4" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0" placeholder="Share what makes this experience unique."></textarea>
                        @error('form.summary') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700" for="available_on">Available On</label>
                            <input id="available_on" wire:model.live="form.available_on" type="date" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700" for="status">Status</label>
                            <select id="status" wire:model.live="form.status" class="w-full rounded-xl border border-[#d4a563]/35 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#7a8730] focus:outline-none focus:ring-0">
                                <option value="draft">Draft</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                            </select>
                            @error('form.status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        @if ($editingTourId)
                            <button type="button" wire:click="cancelEdit" class="rounded-xl border border-[#d4a563]/45 bg-white px-5 py-2.5 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">
                                Cancel
                            </button>
                        @endif

                        <button type="submit" class="rounded-xl bg-[#7a8730] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_12px_28px_-16px_rgba(122,135,48,0.95)] transition hover:bg-[#697629]">
                            {{ $editingTourId ? 'Update Tour' : 'Create Tour' }}
                        </button>
                    </div>
                </form>
            </article>

            <aside class="xl:sticky xl:top-24">
                <article class="overflow-hidden rounded-2xl border border-[#d4a563]/35 bg-white shadow-[0_16px_34px_-20px_rgba(122,85,50,0.55)]">
                    @php
                        $previewPhotos = is_array($tourPhotos ?? null) && count($tourPhotos) > 0 ? $tourPhotos : [];
                        $previewCoverPhotoUrl = null;

                        if ($previewPhotos !== []) {
                            $previewCoverPhotoUrl = $previewPhotos[0]->temporaryUrl();
                        } elseif ($existingGalleryImages !== []) {
                            $previewCoverPhotoUrl = str_starts_with($existingGalleryImages[0], 'data:image/')
                                ? $existingGalleryImages[0]
                                : asset('storage/'.$existingGalleryImages[0]);
                        }
                    @endphp

                    <div class="relative h-44 overflow-hidden">
                        @if ($previewCoverPhotoUrl !== null)
                            <img src="{{ $previewCoverPhotoUrl }}" alt="Tour package preview" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full bg-gradient-to-r from-[#e7d2b0] via-[#f4e8d3] to-[#f8f2e8]"></div>
                        @endif

                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/45 to-transparent"></div>

                        <div class="absolute bottom-3 left-4 flex items-center gap-2.5 rounded-full bg-white/90 px-2.5 py-1.5 shadow-sm backdrop-blur">
                            <div class="h-8 w-8 overflow-hidden rounded-full border border-white bg-[#f7ead7]">
                                @if ($guidePhotoPath !== '')
                                    <img src="{{ asset('storage/'.$guidePhotoPath) }}" alt="Owner profile" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-[#7a5532]">
                                        {{ strtoupper(substr($guideName, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <p class="text-xs text-slate-700">
                                Your guide: <span class="font-semibold text-slate-900">{{ $guideName }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="px-5 pb-5 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Live Preview</p>
                        <h4 class="mt-2 text-xl font-semibold text-slate-900">{{ $form['title'] !== '' ? $form['title'] : 'Tour Title' }}</h4>

                        <p class="mt-2 text-sm text-slate-600">
                            {{ $form['region'] !== '' ? $form['region'] : 'Region' }}
                            @if (($form['city'] ?? '') !== '') · {{ $form['city'] }} @endif
                        </p>

                        <div class="mt-4 space-y-2 rounded-xl border border-[#d4a563]/25 bg-[#fffaf2] p-3">
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">Duration:</span>
                                {{ ($form['duration_hours'] ?? '') !== '' ? $form['duration_hours'].' '.((int) $form['duration_hours'] === 1 ? rtrim($form['duration_unit'] ?? 'hours', 's') : ($form['duration_unit'] ?? 'hours')) : 'Set duration' }}
                            </p>
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">Price:</span>
                                {{ $form['currency'] ?? 'PHP' }}
                                {{ ($form['price_per_person'] ?? '') !== '' ? number_format((float) $form['price_per_person'], 2) : '0.00' }}
                                per {{ $form['price_unit'] ?? 'person' }}
                            </p>
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">Group:</span>
                                @if (($form['min_guests'] ?? '') !== '' || ($form['max_guests'] ?? '') !== '')
                                    {{ $form['min_guests'] !== '' ? $form['min_guests'] : '?' }} - {{ $form['max_guests'] !== '' ? $form['max_guests'] : '?' }} guests
                                @else
                                    Set group range
                                @endif
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @php
                                $previewTransportation = is_array($form['transportation'] ?? null) ? $form['transportation'] : [];
                            @endphp
                            @if ($previewTransportation === [])
                                <span class="rounded-full border border-dashed border-slate-300 px-3 py-1 text-xs text-slate-500">No transportation selected</span>
                            @else
                                @foreach ($previewTransportation as $item)
                                    <span class="rounded-full border border-[#c8cf8a] bg-[#edf2cf] px-3 py-1 text-xs font-medium text-[#6c792a]">{{ str_replace('_', ' ', ucwords($item, '_')) }}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            @if ($previewPhotos !== [])
                                @foreach ($previewPhotos as $photo)
                                    <div class="aspect-square overflow-hidden rounded-lg border border-[#d4a563]/30 bg-white">
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Live tour photo" class="h-full w-full object-cover">
                                    </div>
                                @endforeach
                            @elseif ($existingGalleryImages !== [])
                                @foreach ($existingGalleryImages as $photoPath)
                                    <div class="aspect-square overflow-hidden rounded-lg border border-[#d4a563]/30 bg-white">
                                        <img src="{{ str_starts_with($photoPath, 'data:image/') ? $photoPath : asset('storage/'.$photoPath) }}" alt="Saved tour package photo" class="h-full w-full object-cover">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-3 rounded-lg border border-dashed border-slate-300 px-3 py-4 text-center text-xs text-slate-500">
                                    Add up to 5 tour package photos.
                                </div>
                            @endif
                        </div>

                        <p class="mt-4 text-sm leading-6 text-slate-700">{{ $form['summary'] !== '' ? $form['summary'] : 'Your tour summary will appear here as you type.' }}</p>
                    </div>
                </article>
            </aside>
        </div>

        <section class="mt-6 rounded-2xl border border-[#d4a563]/30 bg-white p-6 shadow-[0_10px_24px_-16px_rgba(122,85,50,0.5)]">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900">Your Tours</h3>
                <span class="text-sm text-slate-500">{{ $tours->count() }} total</span>
            </div>

            @if ($tours->isEmpty())
                <p class="rounded-xl border border-dashed border-[#d4a563]/35 bg-[#fffaf2] p-4 text-sm text-slate-500">No tours yet. Create your first one above.</p>
            @else
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($tours as $tour)
                        <article class="rounded-xl border border-[#d4a563]/30 bg-[#fffaf2] p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900">{{ $tour->title ?? $tour->name }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $tour->region }} @if($tour->city) · {{ $tour->city }} @endif</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $tour->duration_label ?? 'Duration not set' }} · PHP {{ number_format((float) ($tour->price_per_person ?? $tour->price ?? 0), 2) }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-wide text-slate-500">Status: {{ str_replace('_', ' ', $tour->status ?? 'draft') }}</p>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button type="button" wire:click="edit({{ $tour->id }})" class="inline-flex items-center gap-2 rounded-lg border border-[#d4a563]/45 bg-[#fff7ec] px-3 py-2 text-sm font-semibold text-[#7a5532] transition hover:bg-[#f7ead7]">
                                        <i class="fa-solid fa-pen-to-square text-xs" aria-hidden="true"></i>
                                        Edit
                                    </button>
                                    <button type="button" wire:click="delete({{ $tour->id }})" class="inline-flex items-center gap-2 rounded-lg border border-[#d4a563]/45 bg-white px-3 py-2 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">
                                        <i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-6 text-slate-700">{{ $tour->summary ?? $tour->description ?? 'No summary provided.' }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </section>
</div>
