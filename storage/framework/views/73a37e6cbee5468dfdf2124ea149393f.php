<?php
    $tourPreviews = $tours->map(function ($tour) {
        $rawImages = [];
        $transportationItems = [];
        $tourAttributes = (array) $tour->getAttributes();

        if (is_array($tour->activities)) {
            $transportationItems = collect($tour->activities)
                ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
                ->values()
                ->all();
        } elseif (is_string($tour->activities) && trim($tour->activities) !== '') {
            $decodedActivities = json_decode($tour->activities, true);

            if (is_array($decodedActivities) && count($decodedActivities) > 0) {
                $transportationItems = collect($decodedActivities)
                    ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
                    ->values()
                    ->all();
            }
        }

        // Always fallback to category if activities is empty or missing
        if ($transportationItems === [] && is_string($tour->category) && trim($tour->category) !== '') {
            $transportationItems = collect(explode(',', $tour->category))
                ->map(fn (string $item): string => trim($item))
                ->map(fn (string $item): string => strtolower(str_replace(' ', '_', $item)))
                ->filter(fn (string $item): bool => $item !== '' && in_array($item, ['private_transportation', 'public_transportation', 'walking_tour', 'boat_bangka'], true))
                ->values()
                ->all();
        }

            $city = collect(['city', 'city_municipality', 'municipality', 'location'])
                ->map(fn (string $key): string => (string) ($tourAttributes[$key] ?? ''))
                ->first(fn (string $value): bool => trim($value) !== '') ?: 'Not specified';

            $durationLabel = (string) ($tour->duration_label ?? $tour->duration ?? '');
            $durationHours = $tour->duration_hours;
            if ($durationHours === null || $durationHours === '') {
                $durationHours = preg_match('/\d+/', $durationLabel, $durationMatches) === 1 ? (int) $durationMatches[0] : null;
            }

            $durationUnit = (string) ($tour->duration_unit ?? '');
            if ($durationUnit === '') {
                $durationUnit = str_contains(strtolower($durationLabel), 'day') ? 'days' : 'hours';
            }

            $minGuests = (int) ($tour->min_guests ?? 0);
            $maxGuests = (int) ($tour->max_guests ?? 0);
            if ($minGuests === 0 && $maxGuests === 0) {
                $groupSize = collect(['group_size', 'max_people'])
                    ->map(fn (string $key): string => (string) ($tourAttributes[$key] ?? ''))
                    ->first(fn (string $value): bool => trim($value) !== '');

                if ($groupSize !== '') {
                    $minGuests = (int) $groupSize;
                    $maxGuests = (int) $groupSize;
                }
            }

            $availableOn = $tour->available_on;
            if ($availableOn instanceof DateTimeInterface) {
                $availableOn = $availableOn->format('M d, Y');
            } elseif (is_string($availableOn) && trim($availableOn) !== '') {
                $availableOn = substr(trim($availableOn), 0, 10);
            } else {
                $availableOn = 'Not specified';
            }

        foreach ([$tour->featured_image, $tour->image_url, $tour->image_path] as $image) {
            if (is_string($image) && trim($image) !== '') {
                $rawImages[] = trim($image);
            }
        }

        if (is_array($tour->gallery_images)) {
            foreach ($tour->gallery_images as $galleryImage) {
                if (is_string($galleryImage) && trim($galleryImage) !== '') {
                    $rawImages[] = trim($galleryImage);
                }
            }
        }

        $images = collect($rawImages)
            ->map(fn (string $path) => Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/'.$path))
            ->unique()
            ->take(3)
            ->values()
            ->all();

        $transportationSummary = collect($transportationItems)
            ->map(fn (string $item): string => str_replace('_', ' ', ucwords($item, '_')))
            ->implode(', ');

        return [
            'id' => $tour->id,
            'title' => (string) ($tour->name ?? $tour->title ?? 'Untitled tour'),
            'description' => (string) ($tour->description ?? $tour->short_description ?? $tour->summary ?? 'No full description provided.'),
            'summary' => (string) ($tour->short_description ?? $tour->summary ?? ''),
            'generated_summary' => implode('<br>', array_values(array_filter([
                (($tour->price ?? $tour->price_per_person ?? 0) > 0)
                    ? 'Php '.number_format((float) ($tour->price ?? $tour->price_per_person), 2).' per '.((string) ($tour->price_unit ?? 'person'))
                    : null,
                $transportationItems !== [] ? 'Transportation: '.$transportationSummary : null,
                $availableOn !== 'Not specified' ? 'Date: '.$availableOn : null,
                ($minGuests > 0 || $maxGuests > 0)
                    ? (($minGuests ?: 1).' - '.($maxGuests ?: ($minGuests ?: 1)).' guests')
                    : null,
            ]))),
            'price' => (float) ($tour->price ?? $tour->price_per_person ?? 0),
            'price_unit' => (string) ($tour->price_unit ?? 'person'),
            'duration' => $durationHours !== null ? $durationHours.' '.(($durationHours === 1) ? rtrim($durationUnit, 's') : $durationUnit) : ($durationLabel !== '' ? $durationLabel : 'Not specified'),
            'transportation' => $transportationItems,
            'location' => $city !== 'Not specified' ? $city : (string) ($tour->region ?? 'Not specified'),
            'region' => (string) ($tour->region ?? 'Not specified'),
            'available_on' => $availableOn,
            'status' => (string) ($tour->status ?? 'draft'),
            'min_guests' => $minGuests,
            'max_guests' => $maxGuests,
            'images' => $images,
        ];
    })->values();

    $postFeed = $posts->map(function ($post) use ($guide) {
        $rawPostImages = [];
        $likedBy = [];
        $messages = [];

        if (is_array($post->image_paths)) {
            $rawPostImages = collect($post->image_paths)
                ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
                ->values()
                ->all();
        } elseif (is_string($post->image_paths) && trim($post->image_paths) !== '') {
            $decodedImagePaths = json_decode($post->image_paths, true);

            if (is_array($decodedImagePaths)) {
                $rawPostImages = collect($decodedImagePaths)
                    ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
                    ->values()
                    ->all();
            }
        }

        if ($rawPostImages === [] && is_string($post->image_path) && trim($post->image_path) !== '') {
            $rawPostImages[] = trim($post->image_path);
        }

        if (is_array($post->liked_by)) {
            $likedBy = array_values($post->liked_by);
        } elseif (is_string($post->liked_by) && trim($post->liked_by) !== '') {
            $decodedLikedBy = json_decode($post->liked_by, true);
            if (is_array($decodedLikedBy)) {
                $likedBy = array_values($decodedLikedBy);
            }
        }

        if (is_array($post->messages)) {
            $messages = array_values($post->messages);
        } elseif (is_string($post->messages) && trim($post->messages) !== '') {
            $decodedMessages = json_decode($post->messages, true);
            if (is_array($decodedMessages)) {
                $messages = array_values($decodedMessages);
            }
        }

        $resolvedImages = collect($rawPostImages)
            ->map(fn (string $path): string => Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/'.$path))
            ->take(5)
            ->values()
            ->all();

        return [
            'id' => (int) $post->id,
            'text' => (string) ($post->content ?? $post->caption ?? ''),
            'images' => $resolvedImages,
            'likes_count' => max((int) ($post->likes_count ?? count($likedBy)), 0),
            'messages_count' => count($messages),
            'liked_by_current_user' => in_array((int) auth()->id(), $likedBy, true),
            'created_at_human' => $post->created_at?->diffForHumans() ?? 'Just now',
            'guide_name' => (string) ($guide['display_name'] ?? $guide['full_name'] ?? 'Guide'),
            'guide_avatar' => ($guide['profile_photo_path'] ?? '') !== '' ? asset('storage/'.$guide['profile_photo_path']) : null,
        ];
    })->values();
?>

<div
    class="min-h-screen bg-white pb-10"
    x-data="{
        activeTab: 'posts',
        composeBoxOpen: false,
        tourPreviews: <?php echo \Illuminate\Support\Js::from($tourPreviews)->toHtml() ?>,
        postFeed: <?php echo \Illuminate\Support\Js::from($postFeed)->toHtml() ?>,
        selectedTour: null,
        postLightboxImages: [],
        postLightboxIndex: null,
        openTourPreview(tourId) {
            this.selectedTour = this.tourPreviews.find((tour) => Number(tour.id) === Number(tourId)) ?? null;
        },
        closeTourPreview() {
            this.selectedTour = null;
        },
        openPostLightbox(images, index) {
            this.postLightboxImages = images;
            this.postLightboxIndex = index;
        },
        closePostLightbox() {
            this.postLightboxImages = [];
            this.postLightboxIndex = null;
        },
        nextPostImage() {
            if (this.postLightboxImages.length === 0 || this.postLightboxIndex === null) {
                return;
            }

            this.postLightboxIndex = (this.postLightboxIndex + 1) % this.postLightboxImages.length;
        },
        previousPostImage() {
            if (this.postLightboxImages.length === 0 || this.postLightboxIndex === null) {
                return;
            }

            this.postLightboxIndex = (this.postLightboxIndex - 1 + this.postLightboxImages.length) % this.postLightboxImages.length;
        }
    }"
    @keydown.escape.window="closeTourPreview(); closePostLightbox();"
    @post-created.window="composeBoxOpen = false"
>
    <section class="mx-auto w-full max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
        <h1 class="sr-only">Guide Profile</h1>
        <article class="overflow-hidden rounded-lg border border-[#d4a563] bg-white shadow-md">
            <div class="relative h-56 w-full bg-gradient-to-r from-[#7a8f3a] to-[#556b2f]">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guide['cover_photo_path'] !== ''): ?>
                    <img src="<?php echo e(asset('storage/'.$guide['cover_photo_path'])); ?>" alt="Guide cover photo" class="h-full w-full object-cover">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="relative px-6 pb-6 pt-16 sm:px-8">
                <div class="absolute -top-14 left-6 sm:left-8">
                    <div class="h-28 w-28 overflow-hidden rounded-full border-4 border-white bg-[#eef2df] shadow-lg ring-2 ring-[#d4a563]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guide['profile_photo_path'] !== ''): ?>
                            <img src="<?php echo e(asset('storage/'.$guide['profile_photo_path'])); ?>" alt="Guide profile photo" class="h-full w-full object-cover">
                        <?php else: ?>
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#7a8f3a] to-[#556b2f] text-3xl font-bold text-white">
                                <?php echo e(strtoupper(substr($guide['display_name'], 0, 1))); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-3xl font-bold text-slate-900"><?php echo e($guide['display_name']); ?></h3>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide <?php echo e($verificationStatus === 'approved' ? 'bg-[#eef2df] text-[#556b2f]' : 'bg-amber-100 text-amber-700'); ?>">
                                <?php echo e($verificationStatus === 'approved' ? 'Verified' : 'Pending'); ?>

                            </span>
                        </div>

                        <p class="mt-2 text-sm text-slate-600">
                            <?php echo e($guide['region'] ?: 'Region not set'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guide['city_municipality'] !== ''): ?>
                                • <?php echo e($guide['city_municipality']); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guide['barangay'] !== ''): ?>
                                • <?php echo e($guide['barangay']); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guide['bio'] !== ''): ?>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-700"><?php echo e($guide['bio']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="flex flex-col gap-2 sm:w-auto">
                        <a href="<?php echo e(route('dashboard.guide.profile.edit')); ?>" class="inline-flex items-center justify-center rounded-lg border border-[#d4a563] bg-white px-4 py-2 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]">
                            Edit Profile
                        </a>
                        <a href="<?php echo e(route('dashboard.guide')); ?>" class="inline-flex items-center justify-center rounded-lg border border-[#d4a563] bg-[#d4a563] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#bf9155]">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <article class="rounded-lg border border-[#d4a563] bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Completed Tours</p>
                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e($stats['totalToursCompleted']); ?></p>
            </article>

            <article class="rounded-lg border border-[#d4a563] bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Average Rating</p>
                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e(number_format($stats['averageRating'], 1)); ?>/5</p>
                <p class="mt-1 text-xs text-slate-500"><?php echo e($stats['totalReviews']); ?> total reviews</p>
            </article>

            <article class="rounded-lg border border-[#d4a563] bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Experience</p>
                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e($guide['years_of_experience'] !== '' ? $guide['years_of_experience'] : '0'); ?></p>
                <p class="mt-1 text-xs text-slate-500">years guiding travelers</p>
            </article>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-[#d4a563] bg-white shadow-sm">
            <div class="border-b border-[#ead2ad] px-6">
                <div class="flex gap-6">
                    <button type="button" @click="activeTab = 'posts'" :class="activeTab === 'posts' ? 'border-[#d4a563] text-[#7a5532]' : 'border-transparent text-slate-500'" class="border-b-2 px-1 py-4 text-sm font-semibold transition">
                        My Posts (<?php echo e($posts->count()); ?>)
                    </button>
                    <button type="button" @click="activeTab = 'tours'" :class="activeTab === 'tours' ? 'border-[#d4a563] text-[#7a5532]' : 'border-transparent text-slate-500'" class="border-b-2 px-1 py-4 text-sm font-semibold transition">
                        My Tours (<?php echo e($tours->count()); ?>)
                    </button>
                </div>
            </div>

            <div class="bg-[#f6f0e4] p-6" x-show="activeTab === 'posts'" x-cloak>
                <form wire:submit.prevent="createPost" class="mb-6 rounded-2xl border border-[#d4a563] bg-white p-5 shadow-sm">
                    <p class="text-base font-semibold text-[#556b2f]">What's on your mind, <?php echo e($guide['display_name'] ?: 'Diana Grace'); ?>?</p>

                    <div class="mt-3">
                        <textarea
                            wire:model.live="postText"
                            x-on:focus="composeBoxOpen = true"
                            x-on:click="composeBoxOpen = true"
                            x-bind:rows="composeBoxOpen ? 4 : 1"
                            class="w-full resize-none rounded-2xl border border-[#d9c08c] bg-[#fff7ec] px-4 py-3 text-sm text-[#5f3f25] outline-none transition focus:border-[#7a8f3a] focus:bg-white focus:ring-2 focus:ring-[#7a8f3a]/20"
                            placeholder="Share your latest tour experience...."
                        ></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['postText'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div x-show="composeBoxOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" x-cloak class="mt-4 space-y-3">
                        <div>
                            <p class="mb-1 block text-sm font-medium text-[#556b2f]">Upload Photos</p>
                            <input
                                id="post_images"
                                wire:model="newPostImages"
                                wire:key="post-images-input-<?php echo e(count($postImages ?? [])); ?>"
                                type="file"
                                name="post_images[]"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="sr-only"
                            >
                            <label for="post_images" class="inline-flex cursor-pointer items-center rounded-lg bg-[#7a8f3a] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#697629]">
                                Choose up to 5 photos
                            </label>
                        </div>
                        <div wire:loading wire:target="newPostImages" class="text-xs text-[#6c792a]">Uploading photos...</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newPostImages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newPostImages.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['postImages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['postImages.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php
                            $hasNewPostPhotos = is_array($postImages ?? null) && count($postImages) > 0;
                            $postPhotosCount = $hasNewPostPhotos ? count($postImages) : 0;
                        ?>

                        <p class="text-xs text-[#7a5532]">
                            <?php echo e($hasNewPostPhotos ? $postPhotosCount.' of 5 photos selected' : 'No photos uploaded yet. Thumbnails will appear below.'); ?>

                        </p>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNewPostPhotos): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $postImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div wire:key="post-preview-<?php echo e($index); ?>" class="relative h-28 overflow-hidden rounded-lg border border-[#d4a563]/40 bg-[#f4f6eb]">
                                        <img src="<?php echo e($photo->temporaryUrl()); ?>" alt="Post upload preview" class="h-full w-full object-cover">
                                        <button
                                            type="button"
                                            wire:click="removePostImage(<?php echo e($index); ?>)"
                                            class="absolute right-1 top-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/65 text-xs font-bold text-white transition hover:bg-black"
                                            aria-label="Remove photo"
                                        >
                                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="col-span-2 rounded-xl border border-dashed border-[#d4a563]/40 bg-white px-4 py-5 text-center text-xs text-slate-500 sm:col-span-3 md:col-span-5">
                                    No photos uploaded yet.
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="createPost,newPostImages,cancelPostDraft,removePostImage"
                                class="inline-flex items-center rounded-lg bg-[#d4a563] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#bf9155] disabled:cursor-not-allowed disabled:opacity-60"
                                <?php if(trim($postText) === '' || ! $hasNewPostPhotos): echo 'disabled'; endif; ?>
                            >
                                <span wire:loading.remove wire:target="createPost">Post</span>
                                <span wire:loading wire:target="createPost">Posting...</span>
                            </button>
                            <button
                                type="button"
                                wire:click="cancelPostDraft"
                                @click="composeBoxOpen = false"
                                wire:loading.attr="disabled"
                                wire:target="createPost,newPostImages,cancelPostDraft,removePostImage"
                                class="inline-flex items-center rounded-lg border border-[#d4a563] bg-white px-4 py-2 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]"
                            >
                                Cancel
                            </button>
                            <span class="text-xs text-[#7a5532]">JPG, PNG, WebP only</span>
                        </div>
                    </div>
                </form>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isNotEmpty()): ?>
                    <div class="space-y-5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $postCard = $postFeed->firstWhere('id', (int) $post->id);
                                $postCardImages = $postCard['images'] ?? [];
                                $postLiked = (bool) ($postCard['liked_by_current_user'] ?? false);
                            ?>
                            <article wire:key="guide-post-<?php echo e($post->id); ?>" class="rounded-2xl border border-[#d4a563]/80 bg-white shadow-[0_8px_24px_-16px_rgba(85,107,47,0.6)]">
                                <div class="flex items-start justify-between gap-4 px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-11 w-11 overflow-hidden rounded-full border border-[#d4a563]/60 bg-[#eef2df]">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($postCard['guide_avatar'] ?? null) !== null): ?>
                                                <img src="<?php echo e($postCard['guide_avatar']); ?>" alt="Guide avatar" class="h-full w-full object-cover">
                                            <?php else: ?>
                                                <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-[#556b2f]">
                                                    <?php echo e(strtoupper(substr($guide['display_name'] ?? 'G', 0, 1))); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900"><?php echo e($postCard['guide_name'] ?? ($guide['display_name'] ?? 'Guide')); ?></p>
                                            <p class="text-xs text-[#7a5532]"><?php echo e($postCard['created_at_human'] ?? 'Just now'); ?></p>
                                        </div>
                                    </div>

                                    <div class="relative" x-data="{ openMenu: false }">
                                        <button type="button" @click="openMenu = !openMenu" class="rounded-full p-2 text-[#7a5532] transition hover:bg-[#fff7ec] hover:text-[#5f3f25]">⋮</button>
                                        <div x-show="openMenu" x-cloak @click.away="openMenu = false" class="absolute right-0 z-20 mt-1 w-32 rounded-lg border border-[#d4a563]/60 bg-white p-1 shadow-lg">
                                            <button type="button" class="w-full rounded-md px-3 py-2 text-left text-xs font-medium text-[#556b2f] hover:bg-[#f4f6eb]" wire:click="startEditingPost(<?php echo e($post->id); ?>)">Edit</button>
                                            <button type="button" class="w-full rounded-md px-3 py-2 text-left text-xs font-medium text-rose-600 hover:bg-rose-50" wire:click="deletePost(<?php echo e($post->id); ?>)" onclick="return confirm('Delete this post?')">Delete</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-5 pb-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingPostId === (int) $post->id): ?>
                                        <textarea wire:model.live="editingPostText" rows="3" class="w-full rounded-xl border border-[#d9c08c] bg-[#fffef8] px-3 py-2 text-sm text-slate-700 outline-none focus:border-[#7a8f3a] focus:ring-2 focus:ring-[#7a8f3a]/20"></textarea>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editingPostText'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="mt-2 flex items-center gap-2">
                                            <button type="button" wire:click="updatePost" class="rounded-lg bg-[#556b2f] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#465826]">Save</button>
                                            <button type="button" wire:click="cancelEditingPost" class="rounded-lg border border-[#d4a563] px-3 py-1.5 text-xs font-semibold text-[#7a5532] hover:bg-[#fff7ec]">Cancel</button>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm leading-6 text-[#5f3f25]"><?php echo e($postCard['text'] ?? 'No message provided.'); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postCardImages !== []): ?>
                                    <div class="grid gap-1 px-5 pb-4 <?php echo e(count($postCardImages) === 1 ? 'grid-cols-1' : 'grid-cols-2'); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $postCardImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imageIndex => $imageUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button" wire:key="post-image-<?php echo e($post->id); ?>-<?php echo e($imageIndex); ?>" class="overflow-hidden rounded-xl border-2 border-[#b06f3b] bg-[#f6eadb] <?php echo e(count($postCardImages) === 1 ? 'h-[28rem]' : 'h-56'); ?>" @click="openPostLightbox(<?php echo \Illuminate\Support\Js::from($postCardImages)->toHtml() ?>, <?php echo e($imageIndex); ?>)">
                                                <img src="<?php echo e($imageUrl); ?>" alt="Post image" class="h-full w-full object-cover">
                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="border-t border-[#ead2ad] px-5 py-3">
                                    <div class="flex items-center justify-between text-xs text-[#7a5532]">
                                        <p class="inline-flex items-center gap-1"><i class="fa-solid fa-heart" aria-hidden="true"></i> <?php echo e($postCard['likes_count'] ?? 0); ?></p>
                                        <p class="inline-flex items-center gap-1"><i class="fa-solid fa-message" aria-hidden="true"></i> <?php echo e($postCard['messages_count'] ?? 0); ?> messages</p>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2 border-t border-[#f1e2c8] pt-3">
                                        <button type="button" wire:click="toggleLike(<?php echo e($post->id); ?>)" class="inline-flex items-center justify-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold transition <?php echo e($postLiked ? 'bg-rose-50 text-rose-600' : 'bg-[#f8f3e7] text-[#7a5532] hover:bg-[#f1e8d5]'); ?>">
                                            <i class="<?php echo e($postLiked ? 'fa-solid' : 'fa-regular'); ?> fa-heart" aria-hidden="true"></i>
                                            <span>Like</span>
                                        </button>
                                        <button type="button" wire:click="toggleMessageComposer(<?php echo e($post->id); ?>)" class="inline-flex items-center justify-center gap-1 rounded-lg bg-[#f8f3e7] px-3 py-2 text-sm font-semibold text-[#556b2f] transition hover:bg-[#eaf0d6]">
                                            <i class="fa-solid fa-message" aria-hidden="true"></i>
                                            <span>Message</span>
                                        </button>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messageComposerOpen[$post->id] ?? false): ?>
                                        <div class="mt-3 flex items-center gap-2">
                                            <input type="text" wire:model.live="messageInputs.<?php echo e($post->id); ?>" class="w-full rounded-lg border border-[#d9c08c] bg-white px-3 py-2 text-sm text-[#5f3f25] outline-none focus:border-[#7a8f3a] focus:ring-2 focus:ring-[#7a8f3a]/20" placeholder="Type your message to the guide...">
                                            <button type="button" wire:click="sendMessage(<?php echo e($post->id); ?>)" class="rounded-lg bg-[#556b2f] px-3 py-2 text-xs font-semibold text-white hover:bg-[#465826]">Send</button>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['messageInputs.'.$post->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-2xl border border-dashed border-[#d4a563] bg-[#f4f6eb] p-8 text-center">
                        <p class="text-sm text-[#7a5532]">No posts yet. Share your first tour moment.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="p-6" x-show="activeTab === 'tours'" x-cloak>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tours->isNotEmpty()): ?>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="overflow-hidden rounded-lg border border-[#e2c08c] bg-white shadow-sm">
                                <div class="h-44 w-full bg-[#eef2df]">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($tour->featured_image) || ! empty($tour->image_url) || ! empty($tour->image_path)): ?>
                                        <?php
                                            $tourImagePath = (string) ($tour->featured_image ?? $tour->image_url ?? $tour->image_path);
                                            $tourImageSrc = Illuminate\Support\Str::startsWith($tourImagePath, ['http://', 'https://'])
                                                ? $tourImagePath
                                                : asset('storage/'.$tourImagePath);
                                        ?>
                                        <img src="<?php echo e($tourImageSrc); ?>" alt="Tour image" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <div class="flex h-full w-full items-center justify-center text-sm font-medium text-[#556b2f]">No image</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <h4 class="text-lg font-semibold text-slate-900"><?php echo e($tour->name ?? $tour->title ?? 'Untitled tour'); ?></h4>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide <?php echo e(($tourPreviews->firstWhere('id', $tour->id)['status'] ?? 'draft') === 'active' ? 'bg-[#eef2df] text-[#556b2f]' : 'bg-slate-100 text-slate-600'); ?>">
                                            <?php echo e(str_replace('_', ' ', ucfirst($tourPreviews->firstWhere('id', $tour->id)['status'] ?? 'draft'))); ?>

                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        <?php echo e($tourPreviews->firstWhere('id', $tour->id)['location'] ?? $tour->region ?: 'Location not set'); ?>

                                    </p>

                                    <div class="mt-4 rounded-[18px] border border-[#ead8bb] bg-[#fffaf2] px-4 py-3">
                                        <p class="mt-2 text-sm text-slate-700">Price: PHP <?php echo e(number_format((float) ($tour->price ?? $tour->price_per_person ?? 0), 2)); ?> per <?php echo e($tour->price_unit ?? 'person'); ?></p>
                                        <p class="mt-2 text-sm text-slate-700">Group: <?php echo e(($tourPreviews->firstWhere('id', $tour->id)['min_guests'] ?? 1)); ?> - <?php echo e(($tourPreviews->firstWhere('id', $tour->id)['max_guests'] ?? ($tourPreviews->firstWhere('id', $tour->id)['min_guests'] ?? 1))); ?> guests</p>
                                        <p class="mt-2 text-sm text-slate-700">Date: <?php echo e($tourPreviews->firstWhere('id', $tour->id)['available_on'] ?? 'Date not set'); ?></p>
                                    </div>

                                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($tourPreviews->firstWhere('id', $tour->id)['duration'])): ?>
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1"><?php echo e($tourPreviews->firstWhere('id', $tour->id)['duration']); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($tourPreviews->firstWhere('id', $tour->id)['transportation'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transportationLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="rounded-full bg-[#eef2df] px-2.5 py-1 font-medium text-[#556b2f]"><?php echo e(str_replace('_', ' ', ucwords($transportationLabel, '_'))); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <button type="button" class="mt-4 inline-flex rounded-lg border border-[#d4a563] bg-white px-3 py-2 text-xs font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]" @click.stop="openTourPreview(<?php echo e($tour->id); ?>)">
                                        Live Preview
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-dashed border-[#d4a563] bg-[#f4f6eb] p-8 text-center">
                        <p class="text-sm text-slate-600">You do not have any tours yet.</p>
                        <a href="<?php echo e(route('dashboard.guide.tours')); ?>" class="mt-4 inline-flex rounded-lg bg-[#556b2f] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#465826]">
                            Create Tour
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <div x-show="selectedTour" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="closeTourPreview()">
        <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-xl border border-[#d4a563] bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[#ead2ad] bg-white px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900" x-text="selectedTour?.title"></h3>
                <button type="button" class="rounded-lg border border-[#d4a563] px-3 py-1 text-sm font-semibold text-[#7a5532] transition hover:bg-[#fff7ec]" @click="closeTourPreview()">
                    Close
                </button>
            </div>

            <div class="space-y-6 p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <template x-for="(image, imageIndex) in (selectedTour?.images ?? []).slice(0, 3)" :key="imageIndex">
                        <div class="h-48 overflow-hidden rounded-lg border border-[#e2c08c] bg-[#f4f6eb]">
                            <img :src="image" alt="Tour preview image" class="h-full w-full object-cover">
                        </div>
                    </template>
                    <template x-if="(selectedTour?.images ?? []).length === 0">
                        <div class="md:col-span-3 flex h-48 items-center justify-center rounded-lg border border-dashed border-[#d4a563] bg-[#f4f6eb] text-sm text-slate-600">
                            No images available
                        </div>
                    </template>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Price</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTour?.price > 0 ? `Php ${Number(selectedTour.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per ${selectedTour.price_unit}` : 'Not specified'"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Duration</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTour?.duration"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Transportation</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-show="Array.isArray(selectedTour?.transportation) && selectedTour.transportation.length > 0" x-text="selectedTour?.transportation.map((item) => item.replaceAll('_', ' ').replace(/\b\w/g, (character) => character.toUpperCase())).join(', ')"></p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-show="!Array.isArray(selectedTour?.transportation) || selectedTour.transportation.length === 0" x-text="selectedTour?.transportation || 'Not specified'"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Date</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTour?.available_on"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTour?.status ? selectedTour.status.replaceAll('_', ' ').replace(/\b\w/g, (character) => character.toUpperCase()) : 'Draft'"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Location</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTour?.location"></p>
                    </div>
                    <div class="rounded-lg border border-[#e2c08c] p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Guest Range</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="`${selectedTour?.min_guests || 1} - ${selectedTour?.max_guests || selectedTour?.min_guests || 1} guests`"></p>
                    </div>
                </div>

                <div class="rounded-lg border border-[#e2c08c] p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Summary</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700" x-show="selectedTour?.generated_summary" x-html="selectedTour?.generated_summary"></p>
                    <p class="mt-2 text-sm leading-6 text-slate-700" x-show="!selectedTour?.generated_summary" x-text="selectedTour?.summary || 'Not specified'"></p>
                </div>

                <div class="rounded-lg border border-[#e2c08c] p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Full Description</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTour?.description"></p>
                </div>
            </div>
        </div>
    </div>

    <div x-show="postLightboxIndex !== null" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4" @click.self="closePostLightbox()">
        <div class="relative w-full max-w-5xl">
            <button type="button" class="absolute right-0 top-0 z-10 rounded-full bg-white/20 px-3 py-1 text-sm font-semibold text-white backdrop-blur hover:bg-white/30" @click="closePostLightbox()">Close</button>

            <div class="overflow-hidden rounded-2xl border border-white/20 bg-black/30">
                <img :src="postLightboxImages[postLightboxIndex]" alt="Post image preview" class="max-h-[80vh] w-full object-contain">
            </div>

            <div class="mt-3 flex items-center justify-between">
                <button type="button" class="rounded-lg bg-white/20 px-3 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="previousPostImage()">Prev</button>
                <p class="text-xs text-white" x-text="postLightboxIndex !== null ? `${postLightboxIndex + 1} / ${postLightboxImages.length}` : ''"></p>
                <button type="button" class="rounded-lg bg-white/20 px-3 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="nextPostImage()">Next</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/livewire/guide/guide-profile.blade.php ENDPATH**/ ?>