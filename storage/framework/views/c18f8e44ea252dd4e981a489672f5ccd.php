<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="relative left-1/2 right-1/2 min-h-screen w-screen -translate-x-1/2 bg-[#f5efe2] py-10">
        <div class="mx-auto flex w-full max-w-[1260px] flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-6 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <a href="<?php echo e(route('dashboard.tourist')); ?>"
                            class="inline-flex items-center rounded-full border border-[#d8c7a7] bg-[#fff3df] px-4 py-2 text-sm font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                            <span aria-hidden="true" class="mr-1">&larr;</span>
                            Back
                        </a>
                        <br>
                        <br>
                        <h1 class="text-2xl font-bold text-[#3f2d22]">My Bookings</h1>
                        <p class="mt-1 text-sm text-[#6f5d52]">View your booked tours and leave ratings when they are
                            completed.</p>
                    </div>
                </div>
            </section>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <section
                    class="rounded-xl border border-[#dfd0b4] bg-[#eef4e6] px-4 py-3 text-sm font-medium text-[#3f5b2f] shadow-sm">
                    <?php echo e(session('status')); ?>

                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any()): ?>
                <section
                    class="rounded-xl border border-[#e7b8bf] bg-[#fff0f2] px-4 py-3 text-sm font-medium text-[#7f2734] shadow-sm">
                    <?php echo e($errors->first()); ?>

                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-4 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)] sm:p-6">
                <?php
                    $focusedBookingRequest = $selectedBookingRequest ?? null;
                    $focusedBooking = $focusedBookingRequest ? $focusedBookingRequest->booking : null;
                    $focusedReview = $focusedBooking ? $focusedBooking->review : null;
                    $focusedStatus = strtolower((string) ($focusedBooking?->status ?? ($focusedBookingRequest ? $focusedBookingRequest->status : 'pending')));
                    $focusedIsCompleted = $focusedStatus === 'completed' && $focusedBooking !== null;
                    $focusedTourDate = $focusedBooking?->booking_date ?? ($focusedBookingRequest ? $focusedBookingRequest->requested_date : null);
                    $focusedTotalAmount = (float) ($focusedBooking?->total_amount ?? ($focusedBookingRequest ? $focusedBookingRequest->total_price : 0));
                    $focusedGuideName = data_get($focusedBookingRequest, 'guide.full_name') ?: data_get($focusedBookingRequest, 'guide.name') ?: 'Guide';
                    $focusedTourName = data_get($focusedBookingRequest, 'tour.title') ?? (data_get($focusedBookingRequest, 'tour.name') ?? 'Tour Booking');
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bookingRequests->isEmpty()): ?>
                    <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-5 text-sm text-[#7b6756]">
                        No bookings yet. Open a tour and click Book Now to create your first booking request.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.35fr_1fr]">
                        <article class="space-y-4 rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-lg font-semibold text-[#3f2d22]">Booked Tour Packages</h2>
                                <span class="rounded-full bg-[#f5ead7] px-3 py-1 text-xs font-semibold text-[#6f5d52]"><?php echo e($bookingRequests->total()); ?> total</span>
                            </div>

                            <div class="space-y-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bookingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bookingRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $tour = $bookingRequest->tour;
                                        $booking = $bookingRequest->booking;
                                        $status = strtolower((string) ($booking?->status ?? ($bookingRequest->status ?? 'pending')));
                                        $statusLabel = match ($status) {
                                            'accepted', 'confirmed', 'in_progress' => 'Booked',
                                            'completed' => 'Completed',
                                            'declined' => 'Declined',
                                            'cancelled' => 'Cancelled',
                                            default => 'Pending',
                                        };
                                        $statusClasses = match ($status) {
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'confirmed', 'accepted', 'in_progress' => 'bg-[#eef4e6] text-[#4f6d39]',
                                            'declined', 'cancelled' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                        $imageUrl =
                                            $tour?->image_url ??
                                            (($tour?->featured_image ? asset($tour->featured_image) : null) ??
                                                (($tour?->image_path ? asset($tour->image_path) : null) ??
                                                    asset('hero/palawan.jpg')));
                                        $guideName = $bookingRequest->guide?->full_name ?: $bookingRequest->guide?->name ?: 'Guide';
                                        $tourDate = $booking?->booking_date ?? $bookingRequest->requested_date;
                                        $totalAmount = (float) ($booking?->total_amount ?? ($bookingRequest->total_price ?? 0));
                                        $isCompleted = $status === 'completed' && $booking !== null;
                                        $canCancel = $status === 'pending';
                                        $isFocused = (int) ($focusedBookingRequest ? $focusedBookingRequest->id : 0) === (int) $bookingRequest->id;
                                        $focusUrl = route('dashboard.my-bookings', ['focus' => $bookingRequest->id]).'#rating-review-panel';
                                    ?>

                                    <div class="overflow-hidden rounded-2xl border <?php echo e($isFocused ? 'border-[#c8b08d] ring-2 ring-[#efe0c8]' : 'border-[#ddcdb2]'); ?> bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] <?php echo e($isCompleted ? 'cursor-pointer' : ''); ?>"
                                        <?php if($isCompleted): ?> onclick="window.location='<?php echo e($focusUrl); ?>'" <?php endif; ?>>
                                        <img src="<?php echo e($imageUrl); ?>"
                                            alt="<?php echo e($tour?->title ?? ($tour?->name ?? 'Booked Tour')); ?>"
                                            class="h-40 w-full object-cover">

                                        <div class="space-y-3 p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="text-base font-semibold text-[#3f2d22]"><?php echo e($tour?->title ?? ($tour?->name ?? 'Tour Booking')); ?></h3>
                                                    <p class="text-sm text-[#6f5d52]">Guide: <?php echo e($guideName); ?></p>
                                                </div>
                                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClasses); ?>"><?php echo e($statusLabel); ?></span>
                                            </div>

                                            <dl class="grid grid-cols-1 gap-2 text-sm text-[#6f5d52] sm:grid-cols-2">
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Tour Date</dt>
                                                    <dd><?php echo e($tourDate?->format('M d, Y') ?? 'TBD'); ?></dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Group Size</dt>
                                                    <dd><?php echo e($bookingRequest->group_size); ?> <?php echo e($bookingRequest->group_size === 1 ? 'Guest' : 'Guests'); ?></dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Total</dt>
                                                    <dd>₱ <?php echo e(number_format($totalAmount, 2)); ?></dd>
                                                </div>
                                            </dl>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCancel): ?>
                                                    <form method="POST" action="<?php echo e(route('dashboard.my-bookings.cancel', $bookingRequest)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-full border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCompleted): ?>
                                                    <a href="<?php echo e($focusUrl); ?>"
                                                        class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                        Open Rating & Review
                                                    </a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </article>

                        <article id="rating-review-panel"
                            class="h-fit rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5 xl:sticky xl:top-24">
                            <h2 class="text-lg font-semibold text-[#3f2d22]">Rating & Review</h2>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $focusedBookingRequest): ?>
                                <div class="mt-3 rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                                    Select a booking from the left column to review details.
                                </div>
                            <?php else: ?>
                                <div class="mt-3 rounded-xl border border-[#e5d8c2] bg-[#fff9f1] p-4">
                                    <p class="text-sm font-semibold text-[#4f3d31]"><?php echo e($focusedTourName); ?></p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Guide: <?php echo e($focusedGuideName); ?></p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Tour Date: <?php echo e($focusedTourDate?->format('M d, Y') ?? 'TBD'); ?></p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Total: ₱ <?php echo e(number_format($focusedTotalAmount, 2)); ?></p>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $focusedIsCompleted): ?>
                                    <div class="mt-3 rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                                        Ratings are unlocked after completion. Pending bookings can still be cancelled from the left column.
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3 rounded-xl border border-[#d9ceba] bg-[#fff8ef] p-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusedReview): ?>
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-[#4f3d31]">Your current rating</p>
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Rated</span>
                                            </div>
                                            <div class="mt-2 inline-flex items-center gap-1 text-[#d4a563]">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($star = 1; $star <= 5; $star++): ?>
                                                    <i class="<?php echo e($star <= (int) $focusedReview->rating ? 'fa-solid' : 'fa-regular'); ?> fa-star"></i>
                                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <span class="ml-1 text-xs font-semibold text-[#7a6556]"><?php echo e((int) $focusedReview->rating); ?>/5</span>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusedReview->review): ?>
                                                <p class="mt-2 text-sm text-[#6f5d52]"><?php echo e($focusedReview->review); ?></p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?>
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-[#4f3d31]">Share your experience</p>
                                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Pending Review</span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <form method="POST" action="<?php echo e(route('dashboard.my-bookings.rating', $focusedBooking)); ?>" class="mt-3 space-y-3">
                                            <?php echo csrf_field(); ?>

                                            <div>
                                                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Star Rating</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($rating = 5; $rating >= 1; $rating--): ?>
                                                        <label for="rating_<?php echo e($focusedBooking->id); ?>_<?php echo e($rating); ?>"
                                                            class="inline-flex cursor-pointer items-center gap-1 rounded-full border border-[#dcc9ad] bg-[#fffdf8] px-3 py-1.5 text-sm font-semibold text-[#6f5d52] transition hover:border-[#d4a563] hover:text-[#4d3d30]">
                                                            <input id="rating_<?php echo e($focusedBooking->id); ?>_<?php echo e($rating); ?>" type="radio" name="rating" value="<?php echo e($rating); ?>"
                                                                class="h-4 w-4 border-[#c9b28e] text-[#a88452] focus:ring-[#d8c3a2]"
                                                                <?php if((int) old('rating', $focusedReview?->rating ?? 0) === $rating): echo 'checked'; endif; ?>
                                                                required>
                                                            <span class="text-[#d4a563]">★</span>
                                                            <span><?php echo e($rating); ?></span>
                                                        </label>
                                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="review_<?php echo e($focusedBooking->id); ?>"
                                                    class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Comment
                                                    (Optional)</label>
                                                <textarea id="review_<?php echo e($focusedBooking->id); ?>" name="review" rows="3" maxlength="1000"
                                                    class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                                    placeholder="How was your experience?"><?php echo e(old('review', $focusedReview?->review)); ?></textarea>
                                            </div>

                                            <button type="submit"
                                                class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-4 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                <?php echo e($focusedReview ? 'Update Rating' : 'Submit Rating'); ?>

                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bookingRequests->hasPages()): ?>
                        <div class="mt-8">
                            <?php echo e($bookingRequests->links()); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/dashboards/my-bookings.blade.php ENDPATH**/ ?>