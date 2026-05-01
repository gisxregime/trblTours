<div class="bg-gradient-to-b from-slate-50 via-white to-emerald-50 py-10">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-slate-900">Guide Messages</h2>
                    <p class="mt-1 text-sm text-slate-600">Chat with tourists, respond quickly, and keep your bookings moving.</p>
                </div>

                <a href="<?php echo e(route('dashboard.guide')); ?>" class="inline-flex items-center rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">
                    Back to Dashboard
                </a>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.1fr_1.4fr]">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Conversations</h3>
                    <span class="text-sm text-slate-500"><?php echo e($conversations->count()); ?> total</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversations->isEmpty()): ?>
                    <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">No conversations yet.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" wire:click="selectConversation(<?php echo e($conversation->id); ?>)" class="w-full rounded-xl border p-4 text-left transition <?php echo e(($selectedConversation?->id === $conversation->id) ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-slate-50 hover:border-slate-300'); ?>">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900"><?php echo e($conversation->tourist->full_name ?? $conversation->tourist->name ?? 'Tourist'); ?></p>
                                        <p class="mt-1 text-sm text-slate-600"><?php echo e($conversation->tour->title ?? $conversation->tour->name ?? 'General inquiry'); ?></p>
                                        <p class="mt-1 text-xs text-slate-500"><?php echo e($conversation->last_message_at?->diffForHumans() ?? 'No messages yet'); ?></p>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversation->unread_messages_count > 0): ?>
                                        <span class="rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700">
                                            <?php echo e($conversation->unread_messages_count); ?> new
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedConversation): ?>
                    <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">Select a conversation to open the thread.</p>
                <?php else: ?>
                    <div class="mb-4 border-b border-slate-200 pb-3">
                        <p class="font-semibold text-slate-900"><?php echo e($selectedConversation->tourist->full_name ?? $selectedConversation->tourist->name ?? 'Tourist'); ?></p>
                        <p class="mt-1 text-sm text-slate-600"><?php echo e($selectedConversation->tour->title ?? $selectedConversation->tour->name ?? 'General inquiry'); ?></p>
                    </div>

                    <div class="max-h-[430px] space-y-3 overflow-y-auto pr-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selectedConversation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex <?php echo e($message->sender_id === auth()->id() ? 'justify-end' : 'justify-start'); ?>">
                                <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm <?php echo e($message->sender_id === auth()->id() ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800'); ?>">
                                    <p><?php echo e($message->message); ?></p>
                                    <p class="mt-1 text-[11px] <?php echo e($message->sender_id === auth()->id() ? 'text-emerald-100' : 'text-slate-500'); ?>"><?php echo e($message->created_at?->format('M d, h:i A')); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">No messages in this thread yet.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <form wire:submit="sendMessage" class="mt-4 border-t border-slate-200 pt-4">
                        <label for="guideMessageBody" class="mb-2 block text-sm font-semibold text-slate-800">Reply</label>
                        <textarea id="guideMessageBody" wire:model="messageBody" rows="3" maxlength="2000" placeholder="Type your message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['messageBody'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="rounded-lg bg-[#7a8730] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#697629]">
                                Send Message
                            </button>
                        </div>
                    </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </article>
        </section>
    </div>
</div>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/livewire/guide/guide-messages.blade.php ENDPATH**/ ?>