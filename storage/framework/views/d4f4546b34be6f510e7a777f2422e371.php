<div
    class="relative"
    x-data="{ open: false }"
    x-init="if (window.Echo) { window.Echo.private('App.Models.User.<?php echo e((int) auth()->id()); ?>').notification(() => { $wire.$refresh(); }); }"
    @click.outside="open = false"
    wire:poll.30s
>
    <button
        type="button"
        class="icon-button relative"
        aria-label="Notifications"
        @click="open = !open"
    >
        <i class="fa-solid fa-bell text-[16px]"></i>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
            <span class="absolute -right-1 -top-1 inline-flex min-h-[20px] min-w-[20px] items-center justify-center rounded-full bg-[#c0604a] px-1 text-[16px] font-bold leading-none text-white">
                <?php echo e($this->unreadCount > 9 ? '9+' : $this->unreadCount); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition
        class="absolute right-0 top-full z-50 mt-2 w-[min(92vw,22rem)] overflow-hidden rounded-2xl border border-[#d8c7a9] bg-[#fffaf2] shadow-2xl"
    >
        <div class="flex items-center justify-between border-b border-[#ebdec8] px-4 py-3">
            <h3 class="text-sm font-semibold text-[#5a3c2a]">Notifications</h3>
            <button type="button" class="text-[#7a6a58] transition hover:text-[#5a3c2a]" @click="open = false" aria-label="Close notifications">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="max-h-80 overflow-y-auto px-2 py-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isUnread = is_null($notification->read_at);
                    $notificationData = $notification->data;
                    $icon = $notificationData['icon'] ?? 'fa-envelope';
                    $title = $notificationData['title'] ?? 'Update';
                    $message = $notificationData['message'] ?? 'You have a new notification.';
                ?>

                <button
                    type="button"
                    wire:click="openNotification('<?php echo e($notification->id); ?>')"
                    class="mb-1 flex w-full items-start gap-3 rounded-xl px-3 py-2 text-left transition <?php echo e($isUnread ? 'bg-[#fff2df] hover:bg-[#fcead0]' : 'hover:bg-[#f8efdf]'); ?>"
                >
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full <?php echo e($isUnread ? 'bg-[#f7e0bf] text-[#7d583b]' : 'bg-[#f4ead8] text-[#8a7561]'); ?>">
                        <i class="fa-solid <?php echo e($icon); ?> text-xl"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2">
                            <span class="truncate text-sm font-semibold text-[#5a3c2a]"><?php echo e($title); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isUnread): ?>
                                <span class="h-2 w-2 rounded-full bg-[#c0604a]"></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="line-clamp-2 text-xs text-[#7a6654]"><?php echo e($message); ?></span>
                        <span class="mt-1 block text-[11px] text-[#9a836d]"><?php echo e($notification->created_at?->diffForHumans()); ?></span>
                    </span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-3 py-5 text-center">
                    <div class="mx-auto mb-2 inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#f5ebda] text-[#8a7561]">
                        <i class="fa-regular fa-bell"></i>
                    </div>
                    <p class="text-sm font-medium text-[#6c5948]">No notifications yet</p>
                    <p class="mt-1 text-xs text-[#907f6f]">Your travel updates will appear here.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
            <div class="border-t border-[#ebdec8] px-3 py-2">
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="w-full rounded-lg border border-[#d8c7a9] bg-[#fff7ea] px-3 py-2 text-xs font-semibold text-[#6f3e2c] transition hover:bg-[#fbeed8]"
                >
                    Mark all as read
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/livewire/tourist-notifications.blade.php ENDPATH**/ ?>