@extends('themes.default.layouts.app')

@section('title', 'Pendaftaran PPDB Jalur '.$path->name)

@push('meta')
    @include('themes.default.partials.meta-og', ['title' => 'Pendaftaran PPDB Jalur '.$path->name])
@endpush

@php($inputClass = 'w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600')
@php($labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700')
@php($isPrestasi = str_contains($path->slug, 'prestasi'))

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Formulir Pendaftaran — Jalur '.$path->name,
        'subtitle' => 'PPDB '.$path->academicYear->name.' · Pendaftaran ditutup '.tanggal_id($path->closes_at, 'j F Y').' pukul '.$path->closes_at->format('H:i').' WIB.',
        'breadcrumbs' => ['PPDB' => route('ppdb.index'), 'Jalur '.$path->name => null],
    ])

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Indikator langkah: ringkasan 4 bagian formulir --}}
        <ol class="mb-6 grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3" aria-label="Langkah pengisian formulir">
            @foreach(['Data Diri', 'Orang Tua/Wali', 'Sekolah & Nilai', 'Unggah Berkas'] as $stepIndex => $stepLabel)
                <li class="flex items-center gap-2.5 rounded-sm border border-slate-300 bg-white px-3.5 py-2.5 shadow-sm">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">{{ $stepIndex + 1 }}</span>
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-700">{{ $stepLabel }}</span>
                </li>
            @endforeach
        </ol>

        <form action="{{ route('ppdb.store', $path->slug) }}" method="post" enctype="multipart/form-data"
            class="rounded-sm border border-slate-300 bg-white shadow-sm">
            @csrf
            @include('themes.default.partials.card-title', ['title' => 'Formulir Pendaftaran Peserta Didik Baru'])

            <div class="p-5 sm:p-7">
                @if($errors->any())
                    <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="mb-1 font-bold">Periksa kembali isian Anda:</p>
                        <ul class="list-inside list-disc space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Honeypot anti-spam --}}
                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                {{-- Data Diri --}}
                <fieldset>
                    <legend class="mb-4 inline-flex items-center gap-2 border-b-2 border-amber-400 pb-1.5 font-heading text-base font-bold text-primary-800">
                        <span class="flex size-6 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">1</span>
                        Data Diri Calon Peserta Didik
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="{{ $labelClass }}">Nama Lengkap (sesuai akta kelahiran) <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="nisn" class="{{ $labelClass }}">NISN <span class="text-red-500">*</span></label>
                            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required inputmode="numeric" pattern="[0-9]{10}" maxlength="10" placeholder="0061234567" class="{{ $inputClass }}">
                            <p class="mt-1 text-xs text-slate-500">10 digit angka sesuai data Dapodik/kartu NISN. Satu NISN hanya bisa mendaftar sekali per tahun ajaran.</p>
                        </div>
                        <div>
                            <label for="gender" class="{{ $labelClass }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="gender" name="gender" required class="{{ $inputClass }}">
                                <option value="" disabled @selected(! old('gender'))>Pilih...</option>
                                <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="birth_place" class="{{ $labelClass }}">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="birth_date" class="{{ $labelClass }}">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required max="{{ now()->toDateString() }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="religion" class="{{ $labelClass }}">Agama <span class="text-red-500">*</span></label>
                            <select id="religion" name="religion" required class="{{ $inputClass }}">
                                <option value="" disabled @selected(! old('religion'))>Pilih...</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $religion)
                                    <option value="{{ $religion }}" @selected(old('religion') === $religion)>{{ $religion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="phone" class="{{ $labelClass }}">No. HP/WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="{{ $inputClass }}">
                            <p class="mt-1 text-xs text-slate-500">Nomor aktif yang dapat dihubungi panitia.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="{{ $labelClass }}">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" rows="2" required class="{{ $inputClass }}">{{ old('address') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="{{ $labelClass }}">Email <span class="text-xs font-normal text-slate-400">(opsional)</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="{{ $inputClass }}">
                        </div>
                    </div>
                </fieldset>

                {{-- Data Orang Tua --}}
                <fieldset class="mt-8">
                    <legend class="mb-4 inline-flex items-center gap-2 border-b-2 border-amber-400 pb-1.5 font-heading text-base font-bold text-primary-800">
                        <span class="flex size-6 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">2</span>
                        Data Orang Tua/Wali
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="parent_name" class="{{ $labelClass }}">Nama Orang Tua/Wali <span class="text-red-500">*</span></label>
                            <input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name') }}" required class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="parent_phone" class="{{ $labelClass }}">No. HP Orang Tua/Wali <span class="text-red-500">*</span></label>
                            <input type="tel" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}" required class="{{ $inputClass }}">
                        </div>
                    </div>
                </fieldset>

                {{-- Asal Sekolah & Nilai --}}
                <fieldset class="mt-8">
                    <legend class="mb-4 inline-flex items-center gap-2 border-b-2 border-amber-400 pb-1.5 font-heading text-base font-bold text-primary-800">
                        <span class="flex size-6 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">3</span>
                        Asal Sekolah &amp; Nilai
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="school_origin" class="{{ $labelClass }}">Asal Sekolah <span class="text-red-500">*</span></label>
                            <input type="text" id="school_origin" name="school_origin" value="{{ old('school_origin') }}" required placeholder="mis. SMP Negeri 1 Bandung" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="avg_score" class="{{ $labelClass }}">
                                Rata-rata Nilai Rapor
                                @if($isPrestasi)
                                    <span class="text-red-500">*</span>
                                @else
                                    <span class="text-xs font-normal text-slate-400">(opsional)</span>
                                @endif
                            </label>
                            <input type="number" id="avg_score" name="avg_score" value="{{ old('avg_score') }}" step="0.01" min="0" max="100"
                                @required($isPrestasi) class="{{ $inputClass }}">
                            @if($isPrestasi)
                                <p class="mt-1 text-xs text-slate-500">Wajib diisi untuk jalur prestasi: rata-rata nilai rapor semester 1&ndash;5.</p>
                            @endif
                        </div>
                    </div>
                </fieldset>

                {{-- Upload Berkas --}}
                <fieldset class="mt-8">
                    <legend class="mb-4 inline-flex items-center gap-2 border-b-2 border-amber-400 pb-1.5 font-heading text-base font-bold text-primary-800">
                        <span class="flex size-6 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">4</span>
                        Unggah Berkas
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="photo" class="{{ $labelClass }}">Pas Foto <span class="text-red-500">*</span></label>
                            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" required
                                class="w-full rounded border border-slate-300 text-sm text-slate-600 file:mr-3 file:border-0 file:bg-primary-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                            <p class="mt-1 text-xs text-slate-500">JPG/PNG, maksimal 2 MB. Foto formal terbaru berlatar polos.</p>
                        </div>
                        <div>
                            <label for="documents" class="{{ $labelClass }}">Dokumen Pendukung <span class="text-xs font-normal text-slate-400">(opsional, boleh lebih dari satu)</span></label>
                            <input type="file" id="documents" name="documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                                class="w-full rounded border border-slate-300 text-sm text-slate-600 file:mr-3 file:border-0 file:bg-primary-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                            <p class="mt-1 text-xs text-slate-500">Kartu keluarga, akta kelahiran, rapor. PDF/JPG/PNG, maksimal 2 MB per berkas (maks. 5 berkas).</p>
                        </div>
                    </div>
                </fieldset>

                {{-- Pernyataan --}}
                <div class="mt-8 rounded-sm border border-primary-100 bg-primary-50 p-4">
                    <label class="flex cursor-pointer items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="agreement" value="1" required @checked(old('agreement'))
                            class="mt-0.5 size-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600">
                        <span>
                            Saya menyatakan bahwa seluruh data dan berkas yang saya isikan pada formulir ini adalah
                            <strong>benar dan dapat dipertanggungjawabkan</strong>. Apabila di kemudian hari ditemukan
                            ketidaksesuaian data, saya bersedia menerima konsekuensi berupa pembatalan hasil seleksi.
                        </span>
                    </label>
                </div>

                <p class="mt-4 flex items-start gap-2 text-xs leading-relaxed text-slate-500">
                    <svg class="mt-0.5 size-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                    Periksa kembali seluruh isian sebelum mengirim. Setelah terkirim, data hanya dapat
                    dikoreksi oleh panitia — hubungi kontak sekolah bila ada kekeliruan.
                </p>

                <button type="submit"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-sm bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-800 sm:w-auto">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    Kirim Pendaftaran
                </button>
            </div>
        </form>
    </div>
@endsection
