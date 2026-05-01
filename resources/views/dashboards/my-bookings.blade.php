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
                @if ($bookingRequests->isEmpty())
                    <div
                        class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-5 text-sm text-[#7b6756]">
                        No bookings yet. Open a tour and click Book Now to create your first booking request.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        @foreach ($bookingRequests as $bookingRequest)
                            @php
                                $tour = $bookingRequest->tour;
                                $booking = $bookingRequest->booking;
                                $review = $booking?->review;
                                $status = strtolower(
                                    (string) ($booking?->status ?? ($bookingRequest->status ?? 'pending')),
                                );
                                $statusLabel = \Illuminate\Support\Str::headline($status);
                                $statusClasses = match ($status) {
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                    'confirmed', 'accepted' => 'bg-[#eef4e6] text-[#4f6d39]',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'declined', 'cancelled' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-amber-100 text-amber-800',
                                };
                                $imageUrl =
                                    $tour?->image_url ??
                                    (($tour?->featured_image ? asset($tour->featured_image) : null) ??
                                        (($tour?->image_path ? asset($tour->image_path) : null) ??
                                            asset('hero/palawan.jpg')));
                                $guideName =
                                    $bookingRequest->guide?->full_name ?: $bookingRequest->guide?->name ?: 'Guide';
                                $isCompleted = $booking?->status === 'completed';
                                $totalAmount = (float) ($booking?->total_amount ?? ($bookingRequest->total_price ?? 0));
                                $tourDate = $booking?->booking_date ?? $bookingRequest->requested_date;
                                $ratingStateLabel = $review ? 'Rated' : 'Pending Review';
                                $ratingStateClasses = $review
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : 'bg-amber-100 text-amber-800';
                            @endphp

                            <article
                                class="overflow-hidden rounded-2xl border border-[#ddcdb2] bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)]">
                                <img src="{{ $imageUrl }}"
                                    alt="{{ $tour?->title ?? ($tour?->name ?? 'Booked Tour') }}"
                                    class="h-44 w-full object-cover">

                                <div class="space-y-3 p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h2 class="text-lg font-semibold text-[#3f2d22]">
                                                {{ $tour?->title ?? ($tour?->name ?? 'Tour Booking') }}</h2>
                                            <p class="text-sm text-[#6f5d52]">Guide: {{ $guideName }}</p>
                                        </div>
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                                    </div>

                                    <dl class="grid grid-cols-1 gap-2 text-sm text-[#6f5d52] sm:grid-cols-2">
                                        <div>
                                            <dt class="font-semibold text-[#4f3d31]">Tour Date</dt>
                                            <dd>{{ $tourDate?->format('M d, Y') ?? 'TBD' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-[#4f3d31]">Group Size</dt>
                                            <dd>{{ $bookingRequest->group_size }}
                                                {{ $bookingRequest->group_size === 1 ? 'Guest' : 'Guests' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-[#4f3d31]">Total</dt>
                                            <dd>₱ {{ number_format($totalAmount, 2) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-[#4f3d31]">Request ID</dt>
                                            <dd>#{{ $bookingRequest->id }}</dd>
                                        </div>
                                    </dl>

                                    @if ($isCompleted)
                                        <div class="rounded-xl border border-[#d9ceba] bg-[#fff8ef] p-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <h3 class="text-sm font-semibold text-[#4f3d31]">Tour Rating</h3>
                                                <span
                                                    class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $ratingStateClasses }}">{{ $ratingStateLabel }}</span>
                                            </div>

                                            @if ($review)
                                                <p class="mt-1 text-sm text-[#6f5d52]">You rated this tour:</p>
                                                <div class="mt-1 inline-flex items-center gap-1 text-[#d4a563]">
                                                    @for ($star = 1; $star <= 5; $star++)
                                                        <i
                                                            class="{{ $star <= (int) $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                                    @endfor
                                                    <span
                                                        class="ml-1 text-xs font-semibold text-[#7a6556]">{{ (int) $review->rating }}/5</span>
                                                </div>

                                                @if ($review->review)
                                                    <p class="mt-2 text-sm text-[#6f5d52]">{{ $review->review }}</p>
                                                @endif
                                            @else
                                                <form method="POST"
                                                    action="{{ route('dashboard.my-bookings.rating', $booking) }}"
                                                    class="mt-2 space-y-2">
                                                    @csrf
                                                    <div>
                                                        <label for="rating_{{ $booking->id }}"
                                                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Rating</label>
                                                        <select id="rating_{{ $booking->id }}" name="rating" required
                                                            class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                                <option value="{{ $rating }}"
                                                                    @selected((int) old('rating') === $rating)>{{ $rating }}
                                                                    {{ $rating === 1 ? 'Star' : 'Stars' }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="review_{{ $booking->id }}"
                                                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Comment
                                                            (Optional)</label>
                                                        <textarea id="review_{{ $booking->id }}" name="review" rows="2" maxlength="1000"
                                                            class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                                            placeholder="How was your experience?">{{ old('review') }}</textarea>
                                                    </div>

                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-4 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                        Submit Rating
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-3 text-sm text-[#7b6756]">
                                            Rating will be available once this booking is completed.
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
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
