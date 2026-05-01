@props([
    'tour',
    'context' => 'explore',
    'isLiked' => null,
    'enableAjax' => true,
    'imageUrl' => null,
    'guideAvatar' => null,
    'guideName' => null,
    'title' => null,
    'duration' => null,
    'price' => null,
    'rating' => null,
])

@php
    $finalImageUrl = $imageUrl ?? $tour->image_url ?? $tour->featured_image ?? asset('hero/palawan.jpg');
    $finalGuideName = $guideName ?? $tour->marketplaceGuide->name ?? $tour->marketplaceGuide->full_name ?? 'Verified Guide';
    $finalGuideAvatar = $guideAvatar ?? $tour->marketplaceGuide->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($finalGuideName).'&background=f5e8cc&color=3f2d22';
    $finalTitle = $title ?? $tour->title ?? $tour->name;
    $finalRegion = (string) ($tour->region ?? '');
    $finalSummary = (string) ($tour->summary ?? $tour->description ?? '');
    $finalDuration = $duration ?? $tour->duration_label ?? ($tour->duration_hours ? $tour->duration_hours . ' hours' : null) ?? 'Flexible';
    $finalPrice = $price ?? $tour->price ?? $tour->price_per_person ?? $tour->base_price ?? $tour->budget ?? 0;
    $finalRating = $rating ?? $tour->rating ?? '5.0';
    $travelMode = $tour->travel_mode ?? 'Walking';
    $groupLabel = $tour->max_guests ? $tour->max_guests.' pax' : 'Flexible';
    $sourceContext = in_array($context, ['home', 'explore', 'dashboard'], true) ? $context : 'explore';
    $isLikedByCurrentUser = (bool) ($isLiked ?? ($tour->liked_by_current_user ?? false));
    $heartActionLabel = $isLikedByCurrentUser ? 'Remove tour from likes' : 'Save tour to likes';
@endphp

<article class="relative flex flex-col overflow-hidden rounded-[18px] border border-[#e7dcc8] bg-[#f8f5ef] shadow-[0_8px_22px_rgba(52,41,26,0.08)] transition hover:shadow-[0_12px_28px_rgba(52,41,26,0.12)]">
    <a href="{{ route('tours.show', ['tour' => $tour, 'from' => $sourceContext]) }}" data-tour-card-link="tour-{{ $tour->id }}" class="absolute inset-0 z-10" aria-label="View details for {{ $finalTitle }}"></a>

    <div class="relative h-[176px] w-full">
        <img src="{{ $finalImageUrl }}" class="h-full w-full object-cover" alt="{{ $finalTitle }}">

        @auth
            <form method="POST" action="{{ route('dashboard.likes.toggle', $tour) }}" class="absolute right-3 top-3 z-20" data-like-form="true" data-like-ajax="{{ $enableAjax ? 'true' : 'false' }}" data-liked="{{ $isLikedByCurrentUser ? 'true' : 'false' }}">
                @csrf
                <button type="submit" data-like-button="true" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#dfd7c8] bg-white/95 {{ $isLikedByCurrentUser ? 'text-[#b8505b]' : 'text-[#8b7f6e]' }} shadow-sm transition hover:text-[#6d5d4c] disabled:cursor-not-allowed disabled:opacity-70" aria-label="{{ $heartActionLabel }}" aria-pressed="{{ $isLikedByCurrentUser ? 'true' : 'false' }}">
                    <i data-like-icon="true" class="{{ $isLikedByCurrentUser ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="absolute right-3 top-3 z-20 inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#dfd7c8] bg-white/95 text-[#8b7f6e] shadow-sm transition hover:text-[#6d5d4c]" aria-label="Login to like this tour">
                <i class="fa-regular fa-heart"></i>
            </a>
        @endauth

        <div class="absolute -bottom-4 left-3 right-3 flex items-center gap-1.5 overflow-hidden rounded-full border border-[#e3ddd0] bg-white/95 px-2 py-1 shadow-sm">
            <img src="{{ $finalGuideAvatar }}" class="h-5 w-5 rounded-full object-cover border border-[#ede1cd]" alt="{{ $finalGuideName }}">
            <span class="truncate text-[10px] font-medium text-[#8a7f70]">Your Guide: {{ $finalGuideName }}</span>
        </div>
    </div>

    <div class="flex flex-grow flex-col px-4 pb-4 pt-7">
        <h3 class="mb-1 text-[21px] font-semibold leading-tight text-[#5f5447]">{{ $finalTitle }}</h3>

        @if($finalRegion !== '')
            <p class="mb-1 text-[12px] font-medium text-[#7f7262]">{{ $finalRegion }}</p>
        @endif

        @if($finalSummary !== '')
            <p class="mb-2 line-clamp-2 text-[12px] text-[#887a69]">{{ $finalSummary }}</p>
        @endif

        <div class="mb-3 flex items-center gap-1 text-[11px] font-semibold text-[#9b8f7c]">
            <i class="fa-solid fa-star text-[#d4a563] text-[10px]"></i>
            <span>{{ number_format((float) $finalRating, 1) }}</span>
        </div>

        <div class="mt-auto grid gap-2.5 text-[11px] text-[#8e8374]">
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-clock w-3 text-center text-[#8f8475]"></i>
                <span>{{ $finalDuration }}</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-person-walking w-3 text-center text-[#8f8475]"></i>
                <span>{{ $travelMode }}</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-group w-3 text-center text-[#8f8475]"></i>
                <span>{{ $groupLabel }}</span>
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between border-t border-[#e5dcc9] pt-3">
            <span class="text-[11px] font-semibold text-[#877a6a]">Total <span class="text-[16px] font-bold text-[#5d5142]">₱ {{ number_format((float) $finalPrice, 2) }}</span></span>
        </div>
    </div>
</article>

@once
    <script>
        (() => {
            document.addEventListener('submit', async (event) => {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (form.dataset.likeForm !== 'true' || form.dataset.likeAjax !== 'true') {
                    return;
                }

                event.preventDefault();

                if (form.dataset.loading === 'true') {
                    return;
                }

                form.dataset.loading = 'true';

                const button = form.querySelector('[data-like-button="true"]');
                const icon = form.querySelector('[data-like-icon="true"]');

                if (button instanceof HTMLButtonElement) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Like toggle failed.');
                    }

                    const payload = await response.json();
                    const liked = Boolean(payload.liked);

                    form.dataset.liked = liked ? 'true' : 'false';

                    if (button instanceof HTMLButtonElement) {
                        button.classList.toggle('text-[#b8505b]', liked);
                        button.classList.toggle('text-[#8b7f6e]', !liked);
                        button.setAttribute('aria-pressed', liked ? 'true' : 'false');
                        button.setAttribute('aria-label', liked ? 'Remove tour from likes' : 'Save tour to likes');
                    }

                    if (icon instanceof HTMLElement) {
                        icon.classList.toggle('fa-solid', liked);
                        icon.classList.toggle('fa-regular', !liked);
                    }
                } catch (error) {
                    form.submit();
                } finally {
                    form.dataset.loading = 'false';

                    if (button instanceof HTMLButtonElement) {
                        button.disabled = false;
                    }
                }
            });
        })();
    </script>
@endonce
