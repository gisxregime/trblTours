<x-app-layout>
    <div class="relative left-1/2 right-1/2 min-h-screen w-screen -translate-x-1/2 bg-[#f5efe2] py-10">
        <div class="mx-auto flex w-full max-w-[1260px] flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-6 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <a href="{{ route('dashboard.tourist') }}"
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

            @if (session('status'))
                <section
                    class="rounded-xl border border-[#dfd0b4] bg-[#eef4e6] px-4 py-3 text-sm font-medium text-[#3f5b2f] shadow-sm">
                    {{ session('status') }}
                </section>
            @endif

            @if ($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
                <section
                    class="rounded-xl border border-[#e7b8bf] bg-[#fff0f2] px-4 py-3 text-sm font-medium text-[#7f2734] shadow-sm">
                    {{ $errors->first() }}
                </section>
            @endif

            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-4 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)] sm:p-6">
                @php
                    $focusedBookingRequest = $selectedBookingRequest ?? null;
                    $focusedBooking = $focusedBookingRequest ? $focusedBookingRequest->booking : null;
                    $focusedReview = $focusedBooking ? $focusedBooking->review : null;
                    $focusedStatus = strtolower((string) ($focusedBooking?->status ?? ($focusedBookingRequest ? $focusedBookingRequest->status : 'pending')));
                    $focusedIsCompleted = $focusedStatus === 'completed' && $focusedBooking !== null;
                    $focusedTourDate = $focusedBooking?->booking_date ?? ($focusedBookingRequest ? $focusedBookingRequest->requested_date : null);
                    $focusedTotalAmount = (float) ($focusedBooking?->total_amount ?? ($focusedBookingRequest ? $focusedBookingRequest->total_price : 0));
                    $focusedGuideName = data_get($focusedBookingRequest, 'guide.full_name') ?: data_get($focusedBookingRequest, 'guide.name') ?: 'Guide';
                    $focusedTourName = data_get($focusedBookingRequest, 'tour.title') ?? (data_get($focusedBookingRequest, 'tour.name') ?? 'Tour Booking');
                @endphp

                @if ($bookingRequests->isEmpty())
                    <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-5 text-sm text-[#7b6756]">
                        No bookings yet. Open a tour and click Book Now to create your first booking request.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.35fr_1fr]">
                        <article class="space-y-4 rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-lg font-semibold text-[#3f2d22]">Booked Tour Packages</h2>
                                <span class="rounded-full bg-[#f5ead7] px-3 py-1 text-xs font-semibold text-[#6f5d52]">{{ $bookingRequests->total() }} total</span>
                            </div>

                            <div class="space-y-4">
                                @foreach ($bookingRequests as $bookingRequest)
                                    @php
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
                                    @endphp

                                    <div class="overflow-hidden rounded-2xl border {{ $isFocused ? 'border-[#c8b08d] ring-2 ring-[#efe0c8]' : 'border-[#ddcdb2]' }} bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] {{ $isCompleted ? 'cursor-pointer' : '' }}"
                                        @if ($isCompleted) onclick="window.location='{{ $focusUrl }}'" @endif>
                                        <img src="{{ $imageUrl }}"
                                            alt="{{ $tour?->title ?? ($tour?->name ?? 'Booked Tour') }}"
                                            class="h-40 w-full object-cover">

                                        <div class="space-y-3 p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="text-base font-semibold text-[#3f2d22]">{{ $tour?->title ?? ($tour?->name ?? 'Tour Booking') }}</h3>
                                                    <p class="text-sm text-[#6f5d52]">Guide: {{ $guideName }}</p>
                                                </div>
                                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                                            </div>

                                            <dl class="grid grid-cols-1 gap-2 text-sm text-[#6f5d52] sm:grid-cols-2">
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Tour Date</dt>
                                                    <dd>{{ $tourDate?->format('M d, Y') ?? 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Group Size</dt>
                                                    <dd>{{ $bookingRequest->group_size }} {{ $bookingRequest->group_size === 1 ? 'Guest' : 'Guests' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold text-[#4f3d31]">Total</dt>
                                                    <dd>₱ {{ number_format($totalAmount, 2) }}</dd>
                                                </div>
                                            </dl>

                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($canCancel)
                                                    <form method="POST" action="{{ route('dashboard.my-bookings.cancel', $bookingRequest) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-full border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($isCompleted)
                                                    <a href="{{ $focusUrl }}"
                                                        class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-3 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                        Open Rating & Review
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article id="rating-review-panel"
                            class="h-fit rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5 xl:sticky xl:top-24">
                            <h2 class="text-lg font-semibold text-[#3f2d22]">Rating & Review</h2>

                            @if (! $focusedBookingRequest)
                                <div class="mt-3 rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                                    Select a booking from the left column to review details.
                                </div>
                            @else
                                <div class="mt-3 rounded-xl border border-[#e5d8c2] bg-[#fff9f1] p-4">
                                    <p class="text-sm font-semibold text-[#4f3d31]">{{ $focusedTourName }}</p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Guide: {{ $focusedGuideName }}</p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Tour Date: {{ $focusedTourDate?->format('M d, Y') ?? 'TBD' }}</p>
                                    <p class="mt-1 text-sm text-[#6f5d52]">Total: ₱ {{ number_format($focusedTotalAmount, 2) }}</p>
                                </div>

                                @if (! $focusedIsCompleted)
                                    <div class="mt-3 rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                                        Ratings are unlocked after completion. Pending bookings can still be cancelled from the left column.
                                    </div>
                                @else
                                    <div class="mt-3 rounded-xl border border-[#d9ceba] bg-[#fff8ef] p-4">
                                        @if ($focusedReview)
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-[#4f3d31]">Your current rating</p>
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Rated</span>
                                            </div>
                                            <div class="mt-2 inline-flex items-center gap-1 text-[#d4a563]">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    <i class="{{ $star <= (int) $focusedReview->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                                @endfor
                                                <span class="ml-1 text-xs font-semibold text-[#7a6556]">{{ (int) $focusedReview->rating }}/5</span>
                                            </div>

                                            @if ($focusedReview->review)
                                                <p class="mt-2 text-sm text-[#6f5d52]">{{ $focusedReview->review }}</p>
                                            @endif
                                        @else
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-[#4f3d31]">Share your experience</p>
                                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Pending Review</span>
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('dashboard.my-bookings.rating', $focusedBooking) }}" class="mt-3 space-y-3">
                                            @csrf

                                            <div>
                                                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Star Rating</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @for ($rating = 5; $rating >= 1; $rating--)
                                                        <label for="rating_{{ $focusedBooking->id }}_{{ $rating }}"
                                                            class="inline-flex cursor-pointer items-center gap-1 rounded-full border border-[#dcc9ad] bg-[#fffdf8] px-3 py-1.5 text-sm font-semibold text-[#6f5d52] transition hover:border-[#d4a563] hover:text-[#4d3d30]">
                                                            <input id="rating_{{ $focusedBooking->id }}_{{ $rating }}" type="radio" name="rating" value="{{ $rating }}"
                                                                class="h-4 w-4 border-[#c9b28e] text-[#a88452] focus:ring-[#d8c3a2]"
                                                                @checked((int) old('rating', $focusedReview?->rating ?? 0) === $rating)
                                                                required>
                                                            <span class="text-[#d4a563]">★</span>
                                                            <span>{{ $rating }}</span>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>

                                            <div>
                                                <label for="review_{{ $focusedBooking->id }}"
                                                    class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Comment
                                                    (Optional)</label>
                                                <textarea id="review_{{ $focusedBooking->id }}" name="review" rows="3" maxlength="1000"
                                                    class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                                    placeholder="How was your experience?">{{ old('review', $focusedReview?->review) }}</textarea>
                                            </div>

                                            <button type="submit"
                                                class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-4 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                {{ $focusedReview ? 'Update Rating' : 'Submit Rating' }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endif
                        </article>
                    </div>

                    @if ($bookingRequests->hasPages())
                        <div class="mt-8">
                            {{ $bookingRequests->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
