<div class="bg-gradient-to-b from-slate-50 via-white to-emerald-50 py-10" wire:poll.20s>
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-slate-900">Booking Requests</h2>
                    <p class="mt-1 text-sm text-slate-600">Review incoming requests, accept matching bookings, or decline with context.</p>
                </div>

                <a href="<?php echo e(route('dashboard.guide')); ?>" class="inline-flex items-center rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">
                    Back to Dashboard
                </a>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" wire:click="$set('statusFilter', 'all')" class="rounded-full px-3 py-1.5 text-sm font-semibold <?php echo e($statusFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'); ?>">All</button>
                <button type="button" wire:click="$set('statusFilter', 'pending')" class="rounded-full px-3 py-1.5 text-sm font-semibold <?php echo e($statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800 hover:bg-amber-200'); ?>">Pending</button>
                <button type="button" wire:click="$set('statusFilter', 'accepted')" class="rounded-full px-3 py-1.5 text-sm font-semibold <?php echo e($statusFilter === 'accepted' ? 'bg-[#7a8730] text-white' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'); ?>">Accepted</button>
                <button type="button" wire:click="$set('statusFilter', 'declined')" class="rounded-full px-3 py-1.5 text-sm font-semibold <?php echo e($statusFilter === 'declined' ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800 hover:bg-rose-200'); ?>">Declined</button>
                <button type="button" wire:click="$set('statusFilter', 'cancelled')" class="rounded-full px-3 py-1.5 text-sm font-semibold <?php echo e($statusFilter === 'cancelled' ? 'bg-slate-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'); ?>">Cancelled</button>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Requests</h3>
                    <span class="text-sm text-slate-500"><?php echo e($requests->count()); ?> found</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($requests->isEmpty()): ?>
                    <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">No booking requests in this filter.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" wire:click="selectRequest(<?php echo e($request->id); ?>)" class="w-full rounded-xl border p-4 text-left transition <?php echo e(($selectedRequest?->id === $request->id) ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-slate-50 hover:border-slate-300'); ?>">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900"><?php echo e($request->tour->title ?? $request->tour->name ?? 'Tour Request'); ?></p>
                                        <p class="mt-1 text-sm text-slate-600">Tourist: <?php echo e($request->tourist->full_name ?? $request->tourist->name ?? 'Unknown'); ?></p>
                                        <p class="mt-1 text-sm text-slate-600">Date: <?php echo e($request->requested_date?->format('M d, Y') ?? '-'); ?> · Group: <?php echo e($request->group_size); ?></p>
                                    </div>
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold capitalize <?php echo e($request->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'declined' ? 'bg-rose-100 text-rose-700' : ($request->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'))); ?>">
                                        <?php echo e(str_replace('_', ' ', $request->status)); ?>

                                    </span>
                                </div>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Request Details</h3>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedRequest): ?>
                    <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">Select a booking request to view details.</p>
                <?php else: ?>
                    <div class="space-y-3 text-sm text-slate-700">
                        <p><span class="font-semibold text-slate-900">Tour:</span> <?php echo e($selectedRequest->tour->title ?? $selectedRequest->tour->name ?? 'Tour Request'); ?></p>
                        <p><span class="font-semibold text-slate-900">Tourist:</span> <?php echo e($selectedRequest->tourist->full_name ?? $selectedRequest->tourist->name ?? 'Unknown'); ?></p>
                        <p><span class="font-semibold text-slate-900">Requested Date:</span> <?php echo e($selectedRequest->requested_date?->format('M d, Y') ?? '-'); ?></p>
                        <p><span class="font-semibold text-slate-900">Group Size:</span> <?php echo e($selectedRequest->group_size); ?></p>
                        <p><span class="font-semibold text-slate-900">Total Price:</span> PHP <?php echo e(number_format((float) $selectedRequest->total_price, 2)); ?></p>
                        <p><span class="font-semibold text-slate-900">Special Requests:</span> <?php echo e($selectedRequest->special_requests ?: 'None'); ?></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRequest->status === 'declined' && $selectedRequest->decline_reason): ?>
                            <p><span class="font-semibold text-slate-900">Decline Reason:</span> <?php echo e($selectedRequest->decline_reason); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRequest->status === 'pending'): ?>
                        <div class="mt-5 space-y-3 border-t border-slate-200 pt-4">
                            <button type="button" wire:click="acceptRequest(<?php echo e($selectedRequest->id); ?>)" class="w-full rounded-lg bg-[#7a8730] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#697629]">
                                Accept Request
                            </button>

                            <input wire:model.live="declineReasons.<?php echo e($selectedRequest->id); ?>" type="text" maxlength="255" placeholder="Decline reason (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">

                            <button type="button" wire:click="declineRequest(<?php echo e($selectedRequest->id); ?>)" class="w-full rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">
                                Decline Request
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-6 border-t border-slate-200 pt-4">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold text-slate-900">Latest Tourist Reviews</h4>
                        <span class="text-xs text-slate-500">Auto refresh every 20s</span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentReviews->isEmpty()): ?>
                        <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-500">
                            No reviews yet from completed tours.
                        </p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900"><?php echo e($review->tour?->title ?? 'Tour Review'); ?></p>
                                            <p class="text-xs text-slate-600">By <?php echo e($review->tourist?->full_name ?? $review->tourist?->name ?? 'Tourist'); ?></p>
                                        </div>
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700"><?php echo e((int) $review->rating); ?>/5</span>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($review->review)): ?>
                                        <p class="mt-2 text-sm text-slate-700"><?php echo e($review->review); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </article>
        </section>
    </div>
</div>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/livewire/guide/guide-booking-requests.blade.php ENDPATH**/ ?>