<div <?php if($context === 'dashboard'): ?> wire:poll.5s <?php endif; ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFilters): ?>
        <?php
            $filtersActionUrl = $context === 'dashboard'
                ? route('dashboard.tourist')
                : route('explore-tours');
        ?>

        <section class="rounded-2xl border border-[#e4d7be] bg-[#f3ecdf] p-4 sm:p-5">
            <form method="GET" action="<?php echo e($filtersActionUrl); ?>" wire:submit.prevent="filter" class="grid gap-3 md:grid-cols-[1.65fr_0.55fr_auto] md:items-end">
                <label class="grid gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#847766]">
                        <i class="fa-solid fa-location-dot mr-1"></i>Location
                    </span>
                    <input
                        type="text"
                        name="location"
                        wire:model.live.debounce.300ms="locationPath"
                        placeholder="Palawan"
                        class="h-10 rounded-lg border border-[#ddd2bf] bg-[#faf7f2] px-3 text-sm text-[#5f5243] outline-none transition placeholder:text-[#9e9382] focus:border-[#c9a26a]"
                    >
                </label>

                <label class="grid gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#847766]">Sort By</span>
                    <select name="sort_by" wire:model.live="sortBy" class="h-10 rounded-lg border border-[#ddd2bf] bg-[#faf7f2] px-3 text-sm text-[#5f5243] outline-none transition focus:border-[#c9a26a]">
                        <option value="latest">Latest</option>
                        <option value="price_low_high">Price (Low)</option>
                        <option value="price_high_low">Price (High)</option>
                    </select>
                </label>

                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#8ea159] px-5 text-[12px] font-semibold text-white transition hover:brightness-105">
                        Apply
                    </button>
                    <a href="<?php echo e($filtersActionUrl); ?>" wire:click.prevent="resetFilters" class="inline-flex h-10 items-center justify-center rounded-lg border border-[#ddd2bf] bg-[#faf7f2] px-5 text-[12px] font-semibold text-[#736656] transition hover:bg-white">
                        Reset
                    </a>
                </div>
            </form>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-9 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginal030e69daceabf11fdb8cf8b531056084 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal030e69daceabf11fdb8cf8b531056084 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.explore-tour-card','data' => ['tour' => $tour,'context' => $context,'wire:key' => 'tour-card-'.e($tour->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('explore-tour-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tour' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tour),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context),'wire:key' => 'tour-card-'.e($tour->id).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal030e69daceabf11fdb8cf8b531056084)): ?>
<?php $attributes = $__attributesOriginal030e69daceabf11fdb8cf8b531056084; ?>
<?php unset($__attributesOriginal030e69daceabf11fdb8cf8b531056084); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal030e69daceabf11fdb8cf8b531056084)): ?>
<?php $component = $__componentOriginal030e69daceabf11fdb8cf8b531056084; ?>
<?php unset($__componentOriginal030e69daceabf11fdb8cf8b531056084); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full border border-[#d6bc93] rounded-2xl p-12 text-center bg-white shadow-sm">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#f4f6f1] text-[#8f9d59]">
                    <i class="fa-solid fa-route text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-[#3e2a1f] mb-2">No tours available yet</h3>
                <p class="text-[#73543e] mb-6 max-w-md mx-auto">There are currently no tours matching your criteria. Please check back later or modify your search.</p>
                <button wire:click="resetFilters" class="inline-flex items-center justify-center rounded-lg border border-[#c9a26a] bg-[#8f9d59] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#7f8d4d] shadow-sm">
                    Clear Filters
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($tours, 'hasPages') && $tours->hasPages()): ?>
        <div class="mt-10 flex justify-center">
            <?php echo e($tours->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showExploreRequestButton): ?>
        <button id="openRequestModal" class="fixed bottom-6 right-6 z-50 inline-flex min-h-[50px] items-center justify-center rounded-full border-2 bg-[#8f9d59] px-6 text-[16px] font-semibold text-[#ffffff] shadow-[0_10px_24px_rgba(61,47,31,0.16)] transition hover:bg-[#624f27] sm:min-h-[50px] sm:px-10 sm:text-[16px]">
            Create Tour Request
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php /**PATH /home/mistah-regime/tribaltours/resources/views/livewire/explore-tours-feed.blade.php ENDPATH**/ ?>