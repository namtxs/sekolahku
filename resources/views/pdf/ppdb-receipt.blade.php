<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran {{ $applicant->registration_number }}</title>
    <style>
        @page { margin: 36px 48px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }

        .kop { width: 100%; border-bottom: 3px double #014585; padding-bottom: 10px; }
        .kop td { vertical-align: middle; }
        .kop .nama { font-size: 17px; font-weight: bold; color: #014585; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .detail { font-size: 10px; color: #475569; margin-top: 3px; line-height: 1.5; }

        .judul { text-align: center; margin-top: 18px; }
        .judul h1 { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin: 0; color: #0f172a; }
        .judul p { font-size: 10px; color: #64748b; margin: 3px 0 0; }

        .nomor-box { margin: 16px auto 0; width: 60%; border: 2px solid #014585; background: #eaf3ff; text-align: center; padding: 10px 8px; }
        .nomor-box .label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #475569; }
        .nomor-box .nomor { font-size: 26px; font-weight: bold; letter-spacing: 4px; color: #014585; margin-top: 3px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.data th, table.data td { border: 1px solid #cbd5e1; padding: 6px 9px; text-align: left; vertical-align: top; }
        table.data th { background: #eaf3ff; color: #014585; width: 34%; font-weight: bold; }
        table.data td { color: #0f172a; }

        .status { display: inline-block; padding: 2px 10px; border-radius: 9px; font-weight: bold; font-size: 10px; }
        .status-pending { background: #f1f5f9; color: #475569; }
        .status-verified { background: #e0f2fe; color: #075985; }
        .status-accepted { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }

        .catatan { margin-top: 12px; border: 1px solid #fcd34d; background: #fffbeb; padding: 8px 10px; font-size: 10px; color: #92400e; }

        table.ttd { width: 100%; margin-top: 28px; }
        table.ttd td { width: 50%; text-align: center; vertical-align: top; font-size: 11px; }
        .ttd-space { height: 64px; }

        .footer { position: fixed; bottom: -14px; left: 0; right: 0; border-top: 1px solid #cbd5e1; padding-top: 6px; font-size: 8.5px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    {{-- Kop sekolah --}}
    <table class="kop">
        <tr>
            <td style="width: 100%;" align="center">
                <div class="nama">{{ $settings->site_name }}</div>
                <div class="detail">
                    @if(filled($settings->npsn))NPSN: {{ $settings->npsn }}@endif
                    @if(filled($settings->jenjang)) &middot; Jenjang: {{ $settings->jenjang }}@endif<br>
                    @if(filled($settings->address)){{ $settings->address }}<br>@endif
                    @if(filled($settings->phone))Telp. {{ $settings->phone }}@endif
                    @if(filled($settings->email)) &middot; {{ $settings->email }}@endif
                </div>
            </td>
        </tr>
    </table>

    <div class="judul">
        <h1>Bukti Pendaftaran PPDB {{ $applicant->path->academicYear->name }}</h1>
        <p>Penerimaan Peserta Didik Baru &mdash; Jalur {{ $applicant->path->name }}</p>
    </div>

    <div class="nomor-box">
        <div class="label">Nomor Pendaftaran</div>
        <div class="nomor">{{ $applicant->registration_number }}</div>
    </div>

    <table class="data">
        <tr><th>Nama Lengkap</th><td>{{ $applicant->name }}</td></tr>
        <tr><th>NISN</th><td>{{ $applicant->nisn }}</td></tr>
        <tr><th>Jenis Kelamin</th><td>{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $applicant->birth_place }}, {{ tanggal_id($applicant->birth_date) }}</td></tr>
        <tr><th>Agama</th><td>{{ $applicant->religion }}</td></tr>
        <tr><th>Alamat</th><td>{{ $applicant->address }}</td></tr>
        <tr><th>No. HP</th><td>{{ $applicant->phone }}</td></tr>
        @if(filled($applicant->email))
            <tr><th>Email</th><td>{{ $applicant->email }}</td></tr>
        @endif
        <tr><th>Asal Sekolah</th><td>{{ $applicant->school_origin }}</td></tr>
        <tr><th>Nama Orang Tua/Wali</th><td>{{ $applicant->parent_name }}</td></tr>
        <tr><th>No. HP Orang Tua/Wali</th><td>{{ $applicant->parent_phone }}</td></tr>
        @if($applicant->avg_score !== null)
            <tr><th>Rata-rata Nilai Rapor</th><td>{{ number_format((float) $applicant->avg_score, 2, ',', '.') }}</td></tr>
        @endif
        <tr><th>Jalur Pendaftaran</th><td>Jalur {{ $applicant->path->name }} &mdash; Tahun Pelajaran {{ $applicant->path->academicYear->name }}</td></tr>
        <tr><th>Tanggal Mendaftar</th><td>{{ tanggal_id($applicant->created_at) }} pukul {{ $applicant->created_at->format('H:i') }} WIB</td></tr>
        <tr>
            <th>Status Terkini</th>
            <td><span class="status status-{{ $applicant->status }}">{{ $applicant->statusLabel() }}</span></td>
        </tr>
    </table>

    @if(filled($applicant->verification_note))
        <div class="catatan"><strong>Catatan panitia:</strong> {{ $applicant->verification_note }}</div>
    @endif

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                Dicetak pada {{ tanggal_id(now()) }}<br>
                Panitia PPDB {{ $applicant->path->academicYear->name }}
                <div class="ttd-space"></div>
                <strong>( .................................................. )</strong>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dicetak dari sistem PPDB Online {{ $settings->site_name }} dan sah tanpa tanda tangan basah.
        Verifikasi keaslian melalui menu Cek Status di situs resmi sekolah.
    </div>
</body>
</html>
