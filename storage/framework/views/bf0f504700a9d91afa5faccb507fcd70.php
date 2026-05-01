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
    <div class="min-h-screen bg-white py-8">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">
            <section class="rounded-2xl border border-[#d4a563]/35 bg-white p-6 shadow-[0_18px_36px_-22px_rgba(122,85,50,0.5)]">
                <h1 class="text-2xl font-bold text-[#7a5532]">Guide Settings</h1>
                <p class="mt-2 text-sm text-[#8a6746]">Manage your account security.</p>
            </section>

            <section class="rounded-2xl border border-[#d4a563]/35 bg-white p-6 shadow-[0_18px_36px_-22px_rgba(122,85,50,0.5)]">
                <h2 class="text-xl font-semibold text-[#7a5532]">Change Password</h2>

                <form method="POST" action="<?php echo e(route('dashboard.guide.settings.password.update')); ?>" class="mt-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div x-data="{ show: false }">
                        <label for="current_password" class="text-sm font-medium text-[#7a5532]">Current Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="current_password" name="current_password" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="current-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->guidePassword->get('current_password'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guidePassword->get('current_password')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="text-sm font-medium text-[#7a5532]">New Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="password" name="password" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="new-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <p class="mt-1 text-xs text-[#8a6746]">Minimum 8 characters, at least 1 uppercase letter, and 1 number.</p>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->guidePassword->get('password'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guidePassword->get('password')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="text-sm font-medium text-[#7a5532]">Confirm New Password</label>
                        <div class="mt-2 flex rounded-lg border border-[#d4a563]/45 focus-within:border-[#c69958] focus-within:ring-1 focus-within:ring-[#c69958]">
                            <input id="password_confirmation" name="password_confirmation" x-bind:type="show ? 'text' : 'password'" class="w-full rounded-l-lg border-0 px-3 py-2 text-sm text-[#5b3a26] focus:ring-0" required autocomplete="new-password">
                            <button type="button" x-on:click="show = !show" class="rounded-r-lg px-3 text-xs font-semibold text-[#7a5532]">Show/Hide</button>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->guidePassword->get('password_confirmation'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guidePassword->get('password_confirmation')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="reset" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                            Save
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') === 'guide-password-updated'): ?>
                            <p class="text-sm text-[#7a5532]">Password updated.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

                <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'confirm-guide-deletion','show' => $errors->guideDeletion->isNotEmpty(),'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'confirm-guide-deletion','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guideDeletion->isNotEmpty()),'focusable' => true]); ?>
                    <form method="POST" action="<?php echo e(route('dashboard.guide.settings.destroy')); ?>" class="p-6" x-data="{ confirmDelete: <?php echo e(old('confirm_data_deletion') ? 'true' : 'false'); ?> }">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

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
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->guideDeletion->get('password'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guideDeletion->get('password')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
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
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->guideDeletion->get('confirm_data_deletion'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->guideDeletion->get('confirm_data_deletion')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>

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
    
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
                <hr class="my-6 h-0.5 border-t-0 bg-[#c69958]" />
                <!-- Back to Dashboard Button (always bottom) -->
                        <div class="flex justify-end pt-8">
                            <a href="<?php echo e(route('dashboard.guide')); ?>" class="inline-flex items-center rounded-lg bg-[#8f9d59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7f8d4d]">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
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
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/dashboards/guide-settings.blade.php ENDPATH**/ ?>