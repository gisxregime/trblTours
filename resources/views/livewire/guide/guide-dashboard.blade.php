@php
    $guideName = auth()->user()?->full_name ?? auth()->user()?->name ?? 'Local Tour Guide';
    $guideInitials = collect(explode(' ', trim((string) $guideName)))
        ->filter(fn (string $chunk): bool => $chunk !== '')
        ->map(fn (string $chunk): string => strtoupper(substr($chunk, 0, 1)))
        ->take(2)
        ->implode('');

    $ongoingCount = $ongoingTours->count();
    $completedCount = $completedTours->count();
    $declinedCount = $declinedBookings->count();
    $historyCount = $historyBookings->count();
@endphp

<div class="min-h-screen bg-[#f5efe2] py-8" wire:poll.20s>
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard.guide', ['tab' => 'requests']) }}" class="inline-flex items-center gap-2 rounded-full border border-[#6f3e2c]/20 bg-white px-4 py-2 text-sm font-semibold text-[#3f2d22] shadow-[0_10px_22px_rgba(63,45,34,0.12)] transition hover:border-[#8f9d59] hover:text-[#8f9d59]">
            <span aria-hidden="true">&larr;</span>
            <span>Back</span>
        </a>

        <section class="mt-4 rounded-[22px] border border-[#6f3e2c]/15 bg-[#fffaf2] p-5 shadow-[0_14px_36px_rgba(63,45,34,0.14)] sm:p-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#6f5d52]">Guide Control Center</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight text-[#3f2d22] sm:text-4xl">Dashboard</h1>
                    <p class="mt-2 text-sm text-[#6f5d52]">Track booking movement, tour status, and performance in one place.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#d4a563]/45 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-[#5a4a42]">
                    <span>{{ $pendingBookings->count() }} Pending</span>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-4 rounded-[16px] border border-[#6f3e2c]/15 bg-white px-4 py-3 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                <div class="inline-flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#f5e8cc] bg-[#efe1c1] text-sm font-bold text-[#6f5d52]">
                        {{ $guideInitials !== '' ? $guideInitials : 'LG' }}
                    </span>
                    <div>
                        <p class="text-sm font-bold text-[#3f2d22]">{{ $guideName }}</p>
                        <p class="text-xs text-[#6f5d52]">Local Tour Guide</p>
                    </div>
                </div>

                <div class="hidden items-center gap-2 lg:inline-flex">
                    <span class="inline-flex items-center rounded-full border border-[#d4a563]/45 bg-[#fff8eb] px-3 py-1 text-xs font-semibold text-[#5a4a42]">Manage Tours</span>
                    <span class="inline-flex items-center rounded-full border border-[#d4a563]/45 bg-[#fff8eb] px-3 py-1 text-xs font-semibold text-[#5a4a42]">Booking Requests</span>
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.85fr)_minmax(280px,1fr)]">
            <div class="space-y-6">
                <section class="rounded-[22px] border border-[#6f3e2c]/15 bg-[#fff8eb] p-5 shadow-[0_10px_22px_rgba(63,45,34,0.12)] sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-2xl font-black text-[#3f2d22]">Pending Bookings</h2>
                        <span class="inline-flex w-fit items-center rounded-full border border-[#d4a563]/35 bg-white px-3 py-1 text-sm font-semibold text-[#6f5d52]">{{ $pendingBookings->count() }} waiting</span>
                    </div>

                    @if ($actionMessage)
                        <p class="mt-4 rounded-xl border border-[#d4a563]/30 bg-white px-4 py-3 text-sm font-semibold text-[#5b3a26]">{{ $actionMessage }}</p>
                    @endif

                    @if ($pendingBookings->isEmpty())
                        <p class="mt-4 rounded-xl border border-dashed border-[#d9c3a0] bg-white p-4 text-sm text-[#6f5d52]">No pending bookings.</p>
                    @else
                        <div class="mt-4 grid gap-4">
                            @foreach ($pendingBookings as $booking)
                                <article wire:key="pending-{{ $booking->id }}" class="rounded-2xl border border-[#d4a563]/30 bg-white p-4 shadow-[0_10px_20px_-16px_rgba(122,85,50,0.5)]">
                                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                        <div class="grid gap-1 text-sm text-[#6f5d52]">
                                            <p><span class="font-semibold text-[#3f2d22]">Tourist:</span> {{ $booking->tourist->full_name ?? $booking->tourist->name ?? 'Unknown' }}</p>
                                            <p><span class="font-semibold text-[#3f2d22]">Tour date:</span> {{ $booking->requested_date?->format('M d, Y') ?? '-' }}</p>
                                            <p><span class="font-semibold text-[#3f2d22]">Tour title:</span> {{ $booking->tour->title ?? $booking->tour->name ?? 'Tour package' }}</p>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" wire:click="acceptPending({{ $booking->id }})" class="inline-flex items-center rounded-full border border-[#8f9d59] bg-[#8f9d59] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#7a894b]">
                                                Accept
                                            </button>
                                            <button type="button" wire:click="declinePending({{ $booking->id }})" class="inline-flex items-center rounded-full border border-[#b1544d] bg-[#b1544d] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#9d4741]">
                                                Decline
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <article class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-6 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                        <h3 class="text-2xl font-black text-[#3f2d22]">On Going Tours</h3>

                        @if ($ongoingTours->isEmpty())
                            <p class="mt-4 rounded-xl border border-dashed border-[#d4a563]/35 bg-[#fff8eb] p-4 text-sm text-[#6f5d52]">No on going tours.</p>
                        @else
                            <div class="mt-4 space-y-3">
                                @foreach ($ongoingTours as $booking)
                                    <article wire:key="ongoing-{{ $booking->id }}" class="rounded-xl border border-[#d4a563]/25 bg-[#fffaf2] p-4">
                                        <p class="text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Tourist:</span> {{ $booking->tourist->full_name ?? $booking->tourist->name ?? 'Unknown' }}</p>
                                        <p class="mt-1 text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Tour date:</span> {{ $booking->requested_date?->format('M d, Y') ?? '-' }}</p>
                                        <p class="mt-1 text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Tour title:</span> {{ $booking->tour->title ?? $booking->tour->name ?? 'Tour package' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <article class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-6 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                        <h3 class="text-2xl font-black text-[#3f2d22]">Completed Tours</h3>

                        @if ($completedTours->isEmpty())
                            <p class="mt-4 rounded-xl border border-dashed border-[#d4a563]/35 bg-[#fff8eb] p-4 text-sm text-[#6f5d52]">No completed tours with feedback yet.</p>
                        @else
                            <div class="mt-4 space-y-3">
                                @foreach ($completedTours as $completed)
                                    <article wire:key="completed-{{ $completed->id }}" class="rounded-xl border border-[#d4a563]/25 bg-[#fffaf2] p-4">
                                        <p class="text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Tourist:</span> {{ $completed->tourist->full_name ?? $completed->tourist->name ?? 'Unknown' }}</p>
                                        <p class="mt-1 text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Tour date:</span> {{ $completed->booking?->booking_date?->format('M d, Y') ?? '-' }}</p>
                                        <p class="mt-1 text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Rating:</span> {{ str_repeat('★', (int) $completed->rating) }}{{ str_repeat('☆', 5 - (int) $completed->rating) }}</p>
                                        <p class="mt-1 text-sm text-[#6f5d52]"><span class="font-semibold text-[#3f2d22]">Comment:</span> {{ $completed->review ?: 'No comment' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </article>
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <article class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-6 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                        <h3 class="text-2xl font-black text-[#3f2d22]">Declined Bookings</h3>

                        @if ($declinedBookings->isEmpty())
                            <p class="mt-4 rounded-xl border border-dashed border-[#d4a563]/35 bg-[#fff8eb] p-4 text-sm text-[#6f5d52]">No declined bookings.</p>
                        @else
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#ecd3ad] text-sm">
                                    <thead class="bg-[#fff8eb]">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Tourist name</th>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Tour date</th>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Tour title</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#f0e0c4] bg-white">
                                        @foreach ($declinedBookings as $booking)
                                            <tr wire:key="declined-{{ $booking->id }}">
                                                <td class="px-3 py-2 text-[#3f2d22]">{{ $booking->tourist->full_name ?? $booking->tourist->name ?? 'Unknown' }}</td>
                                                <td class="px-3 py-2 text-[#6f5d52]">{{ $booking->requested_date?->format('M d, Y') ?? '-' }}</td>
                                                <td class="px-3 py-2 text-[#6f5d52]">{{ $booking->tour->title ?? $booking->tour->name ?? 'Tour package' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </article>

                    <article class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-6 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                        <h3 class="text-2xl font-black text-[#3f2d22]">History Bookings</h3>

                        @if ($historyBookings->isEmpty())
                            <p class="mt-4 rounded-xl border border-dashed border-[#d4a563]/35 bg-[#fff8eb] p-4 text-sm text-[#6f5d52]">No history bookings.</p>
                        @else
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#ecd3ad] text-sm">
                                    <thead class="bg-[#fff8eb]">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Tourist Name</th>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Tour Date</th>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Status</th>
                                            <th class="px-3 py-2 text-left font-semibold text-[#3f2d22]">Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#f0e0c4] bg-white">
                                        @foreach ($historyBookings as $booking)
                                            <tr wire:key="history-{{ $booking->id }}">
                                                <td class="px-3 py-2 text-[#3f2d22]">{{ $booking->tourist->full_name ?? $booking->tourist->name ?? 'Unknown' }}</td>
                                                <td class="px-3 py-2 text-[#6f5d52]">{{ $booking->requested_date?->format('M d, Y') ?? '-' }}</td>
                                                <td class="px-3 py-2 text-[#6f5d52]">{{ ucfirst((string) $booking->status) }}</td>
                                                <td class="px-3 py-2 text-[#6f5d52]">{{ $booking->booking?->review?->rating ? ((string) $booking->booking->review->rating).' ★' : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </article>
                </section>
            </div>

            <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
                <section class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-5 shadow-[0_14px_36px_rgba(63,45,34,0.14)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6f5d52]">Summary</p>

                    <div class="mt-3 space-y-3">
                        <div class="flex items-baseline justify-between border-b border-[#f0e0c4] pb-2">
                            <span class="text-sm text-[#6f5d52]">Total Earnings</span>
                            <span class="text-2xl font-black text-[#3f2d22]">₱{{ number_format((float) $totalEarnings, 0) }}</span>
                        </div>
                        <div class="flex items-baseline justify-between border-b border-[#f0e0c4] pb-2">
                            <span class="text-sm text-[#6f5d52]">Average Ratings</span>
                            <span class="text-xl font-black text-[#3f2d22]">{{ number_format($averageRatings, 1) }} ★</span>
                        </div>
                        <div class="flex items-baseline justify-between border-b border-[#f0e0c4] pb-2">
                            <span class="text-sm text-[#6f5d52]">Total Feedbacks</span>
                            <span class="text-xl font-black text-[#3f2d22]">{{ $totalFeedbacks }}</span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-sm text-[#6f5d52]">Total Bookings</span>
                            <span class="text-xl font-black text-[#3f2d22]">{{ $totalBookings }}</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-[22px] border border-[#6f3e2c]/15 bg-white p-5 shadow-[0_10px_22px_rgba(63,45,34,0.12)]">
                    <h3 class="text-xl font-black text-[#3f2d22]">Activity</h3>
                    <div class="mt-3 grid gap-2 text-sm text-[#6f5d52]">
                        <div class="inline-flex items-center justify-between rounded-xl border border-[#e7d2a9] bg-[#fff8eb] px-3 py-2">
                            <span>Pending</span>
                            <span class="font-bold text-[#3f2d22]">{{ $pendingBookings->count() }}</span>
                        </div>
                        <div class="inline-flex items-center justify-between rounded-xl border border-[#e7d2a9] bg-[#fff8eb] px-3 py-2">
                            <span>On Going</span>
                            <span class="font-bold text-[#3f2d22]">{{ $ongoingCount }}</span>
                        </div>
                        <div class="inline-flex items-center justify-between rounded-xl border border-[#e7d2a9] bg-[#fff8eb] px-3 py-2">
                            <span>Completed</span>
                            <span class="font-bold text-[#3f2d22]">{{ $completedCount }}</span>
                        </div>
                        <div class="inline-flex items-center justify-between rounded-xl border border-[#e7d2a9] bg-[#fff8eb] px-3 py-2">
                            <span>Declined</span>
                            <span class="font-bold text-[#3f2d22]">{{ $declinedCount }}</span>
                        </div>
                        <div class="inline-flex items-center justify-between rounded-xl border border-[#e7d2a9] bg-[#fff8eb] px-3 py-2">
                            <span>History</span>
                            <span class="font-bold text-[#3f2d22]">{{ $historyCount }}</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
