{{-- Kartu CTA PPDB di beranda: tampil hanya bila ada jalur yang sedang dibuka. Variabel: $ppdbOpenPaths --}}
@if(($ppdbOpenPaths ?? collect())->isNotEmpty())
    <section class="mt-4 overflow-hidden rounded-sm border border-amber-300 bg-gradient-to-r from-primary-800 to-primary-600 shadow-sm" aria-label="Pendaftaran PPDB dibuka">
        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-start gap-4">
                <span class="hidden size-12 shrink-0 items-center justify-center rounded-full bg-amber-400 text-primary-950 sm:flex">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-400">Pendaftaran Dibuka</p>
                    <h2 class="mt-1 font-heading text-lg font-bold text-white">PPDB Online {{ $ppdbOpenPaths->first()->academicYear->name }}</h2>
                    <p class="mt-1 text-sm text-primary-100">
                        Jalur {{ $ppdbOpenPaths->pluck('name')->join(', ', ' dan ') }} sedang menerima pendaftaran.
                        Daftarkan putra-putri Anda sekarang secara daring.
                    </p>
                </div>
            </div>
            <a href="{{ route('ppdb.index') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-sm bg-amber-400 px-6 py-3 text-sm font-bold text-primary-950 shadow-sm transition hover:bg-amber-300">
                Daftar Sekarang
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </section>
@endif
