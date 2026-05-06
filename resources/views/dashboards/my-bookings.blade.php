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
                    <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-5 text-sm text-[#7b6756]">
                        No bookings yet. Open a tour and click Book Now to create your first booking request.
                    </div>
                @else
                    @php
                        $pendingBookings = collect();
                        $scheduledBookings = collect();
                        $completedBookings = collect();

                        foreach ($bookingRequests as $bookingRequest) {
                            $status = strtolower((string) ($bookingRequest->booking?->status ?? ($bookingRequest->status ?? 'pending')));

                            if ($status === 'pending') {
                                $pendingBookings->push($bookingRequest);

                                continue;
                            }

                            if (in_array($status, ['accepted', 'confirmed', 'in_progress', 'booked'], true)) {
                                $scheduledBookings->push($bookingRequest);

                                continue;
                            }

                            if ($status === 'completed' && $bookingRequest->booking !== null) {
                                $completedBookings->push($bookingRequest);
                            }
                        }
                    @endphp

                    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                        <article class="space-y-4 rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5">
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-lg font-semibold text-[#3f2d22]">Pending</h2>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ $pendingBookings->count() }}</span>
                            </div>

                            @if ($pendingBookings->isEmpty())
                                <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">No pending bookings.</div>
                            @else
                                <div class="space-y-4">
                                    @foreach ($pendingBookings as $bookingRequest)
                                        @php
                                            $tour = $bookingRequest->tour;
                                            $booking = $bookingRequest->booking;
                                            $imageUrl =
                                                $tour?->image_url ??
                                                (($tour?->featured_image ? asset($tour->featured_image) : null) ??
                                                    (($tour?->image_path ? asset($tour->image_path) : null) ??
                                                        asset('hero/palawan.jpg')));
                                            $guideName = $bookingRequest->guide?->full_name ?: $bookingRequest->guide?->name ?: 'Guide';
                                            $tourDate = $booking?->booking_date ?? $bookingRequest->requested_date;
                                            $totalAmount = (float) ($booking?->total_amount ?? ($bookingRequest->total_price ?? 0));
                                        @endphp

                                        <div class="overflow-hidden rounded-2xl border border-[#ddcdb2] bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)]">
                                            <img src="{{ $imageUrl }}" alt="{{ $tour?->title ?? ($tour?->name ?? 'Booked Tour') }}" class="h-32 w-full object-cover">

                                            <div class="space-y-3 p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h3 class="text-base font-semibold text-[#3f2d22]">{{ $tour?->title ?? ($tour?->name ?? 'Tour Booking') }}</h3>
                                                        <p class="text-sm text-[#6f5d52]">Guide: {{ $guideName }}</p>
                                                    </div>
                                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Pending</span>
                                                </div>

                                                <p class="text-sm text-[#6f5d52]">{{ $tourDate?->format('M d, Y') ?? 'TBD' }} • ₱ {{ number_format($totalAmount, 2) }}</p>

                                                <form method="POST" action="{{ route('dashboard.my-bookings.cancel', $bookingRequest) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center rounded-full border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="space-y-4 rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5">
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-lg font-semibold text-[#3f2d22]">Booked</h2>
                                <span class="rounded-full bg-[#eef4e6] px-2.5 py-1 text-xs font-semibold text-[#4f6d39]">{{ $scheduledBookings->count() }}</span>
                            </div>

                            @if ($scheduledBookings->isEmpty())
                                <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">No scheduled bookings yet.</div>
                            @else
                                <div class="space-y-4">
                                    @foreach ($scheduledBookings as $bookingRequest)
                                        @php
                                            $tour = $bookingRequest->tour;
                                            $booking = $bookingRequest->booking;
                                            $status = strtolower((string) ($booking?->status ?? ($bookingRequest->status ?? 'accepted')));
                                            $statusLabel = in_array($status, ['accepted', 'confirmed', 'booked'], true) ? 'Booked' : 'In Progress';
                                            $imageUrl =
                                                $tour?->image_url ??
                                                (($tour?->featured_image ? asset($tour->featured_image) : null) ??
                                                    (($tour?->image_path ? asset($tour->image_path) : null) ??
                                                        asset('hero/palawan.jpg')));
                                            $guideName = $bookingRequest->guide?->full_name ?: $bookingRequest->guide?->name ?: 'Guide';
                                            $tourDate = $booking?->booking_date ?? $bookingRequest->requested_date;
                                            $totalAmount = (float) ($booking?->total_amount ?? ($bookingRequest->total_price ?? 0));
                                        @endphp

                                        <div class="overflow-hidden rounded-2xl border border-[#ddcdb2] bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)]">
                                            <img src="{{ $imageUrl }}" alt="{{ $tour?->title ?? ($tour?->name ?? 'Scheduled Tour') }}" class="h-32 w-full object-cover">

                                            <div class="space-y-2 p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h3 class="text-base font-semibold text-[#3f2d22]">{{ $tour?->title ?? ($tour?->name ?? 'Tour Booking') }}</h3>
                                                        <p class="text-sm text-[#6f5d52]">Guide: {{ $guideName }}</p>
                                                    </div>
                                                    <span class="rounded-full bg-[#eef4e6] px-2.5 py-1 text-xs font-semibold text-[#4f6d39]">{{ $statusLabel }}</span>
                                                </div>

                                                <p class="text-sm text-[#6f5d52]">{{ $tourDate?->format('M d, Y') ?? 'TBD' }} • ₱ {{ number_format($totalAmount, 2) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="space-y-4 rounded-2xl border border-[#decfb2] bg-[#fffdf8] p-4 shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)] sm:p-5">
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-lg font-semibold text-[#3f2d22]">Completed</h2>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $completedBookings->count() }}</span>
                            </div>

                            @if ($completedBookings->isEmpty())
                                <div class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">No completed tours yet.</div>
                            @else
                                <div class="space-y-4">
                                    @foreach ($completedBookings as $bookingRequest)
                                        @php
                                            $tour = $bookingRequest->tour;
                                            $booking = $bookingRequest->booking;
                                            $review = $booking?->review;
                                            $imageUrl =
                                                $tour?->image_url ??
                                                (($tour?->featured_image ? asset($tour->featured_image) : null) ??
                                                    (($tour?->image_path ? asset($tour->image_path) : null) ??
                                                        asset('hero/palawan.jpg')));
                                            $guideName = $bookingRequest->guide?->full_name ?: $bookingRequest->guide?->name ?: 'Guide';
                                            $tourDate = $booking?->booking_date ?? $bookingRequest->requested_date;
                                            $totalAmount = (float) ($booking?->total_amount ?? ($bookingRequest->total_price ?? 0));
                                        @endphp

                                        <div class="overflow-hidden rounded-2xl border border-[#ddcdb2] bg-[#fffdf8] shadow-[0_16px_35px_-30px_rgba(63,45,34,0.7)]">
                                            <img src="{{ $imageUrl }}" alt="{{ $tour?->title ?? ($tour?->name ?? 'Completed Tour') }}" class="h-32 w-full object-cover">

                                            <div class="space-y-3 p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h3 class="text-base font-semibold text-[#3f2d22]">{{ $tour?->title ?? ($tour?->name ?? 'Tour Booking') }}</h3>
                                                        <p class="text-sm text-[#6f5d52]">Guide: {{ $guideName }}</p>
                                                    </div>
                                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Completed</span>
                                                </div>

                                                <p class="text-sm text-[#6f5d52]">{{ $tourDate?->format('M d, Y') ?? 'TBD' }} • ₱ {{ number_format($totalAmount, 2) }}</p>

                                                @if ($review)
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Your Rating</p>
                                                        <div class="mt-1 inline-flex items-center gap-1 text-[#d4a563]">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <i class="{{ $star <= (int) $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                                            @endfor
                                                            <span class="ml-1 text-xs font-semibold text-[#7a6556]">{{ (int) $review->rating }}/5</span>
                                                        </div>
                                                        @if ($review->review)
                                                            <p class="mt-2 text-sm text-[#6f5d52]">{{ $review->review }}</p>
                                                        @endif
                                                    </div>
                                                @endif

                                                <form method="POST" action="{{ route('dashboard.my-bookings.rating', $booking) }}" class="space-y-2">
                                                    @csrf
                                                    <div>
                                                        <label for="rating_{{ $booking->id }}" class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Star Rating</label>
                                                        <select id="rating_{{ $booking->id }}" name="rating" required class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]">
                                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                                <option value="{{ $rating }}" @selected((int) old('rating', $review?->rating ?? 0) === $rating)>{{ $rating }} {{ $rating === 1 ? 'Star' : 'Stars' }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="review_{{ $booking->id }}" class="mb-1 block text-xs font-semibold uppercase tracking-[0.07em] text-[#6f5d52]">Comment (Optional)</label>
                                                        <textarea id="review_{{ $booking->id }}" name="review" rows="3" maxlength="1000"
                                                            class="w-full rounded-lg border border-[#dcc9ad] bg-[#fffdf8] px-3 py-2 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"
                                                            placeholder="How was your experience?">{{ old('review', $review?->review) }}</textarea>
                                                    </div>

                                                    <button type="submit" class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-4 py-1.5 text-xs font-semibold text-[#f7fff4] transition hover:bg-[#4f7740]">
                                                        {{ $review ? 'Update Rating' : 'Submit Rating' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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
