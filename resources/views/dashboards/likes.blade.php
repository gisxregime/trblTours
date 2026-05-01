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
                        <h1 class="text-2xl font-bold text-[#3f2d22]">Liked Tours</h1>
                        <p class="mt-1 text-sm text-[#6f5d52]">Your saved tours are listed here.</p>
                    </div>
                </div>
            </section>

            @if (session('status'))
                <section
                    class="rounded-xl border border-[#dfd0b4] bg-[#eef4e6] px-4 py-3 text-sm font-medium text-[#3f5b2f] shadow-sm">
                    {{ session('status') }}
                </section>
            @endif

            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-4 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)] sm:p-6">
                @if ($likedTours->isEmpty())
                    <div
                        class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-5 text-sm text-[#7b6756]">
                        You have no liked tours yet. Tap the heart icon on a tour to save it here.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($likedTours as $tour)
                            <x-explore-tour-card :tour="$tour" context="dashboard" :is-liked="true"
                                :enable-ajax="false" />
                        @endforeach
                    </div>

                    @if ($likedTours->hasPages())
                        <div class="mt-8">
                            {{ $likedTours->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
