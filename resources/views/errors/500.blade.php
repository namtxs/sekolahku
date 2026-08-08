<!DOCTYPE html>
{{-- Halaman error mandiri: tanpa layout tema, tanpa query database, tanpa
     aset build — harus tetap tampil saat server/database bermasalah. --}}
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terjadi Kesalahan</title>
    <style>
        body { margin: 0; display: flex; min-height: 100vh; align-items: center; justify-content: center; background: #eaf3ff; font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; color: #1e293b; }
        .card { max-width: 26rem; margin: 1rem; padding: 3rem 2.5rem; text-align: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 0.25rem; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05); }
        .code { margin: 0; font-size: 4.5rem; font-weight: 800; line-height: 1; color: #023a6e; }
        .rule { display: inline-block; width: 4rem; height: 0.25rem; margin-top: 1rem; border-radius: 9999px; background: #fbbf24; }
        h1 { margin: 0.75rem 0 0; font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        p { margin: 0.75rem 0 0; font-size: 0.875rem; line-height: 1.6; color: #64748b; }
        a.btn { display: inline-block; margin-top: 2rem; padding: 0.625rem 1.5rem; border-radius: 0.25rem; background: #0f6fc7; font-size: 0.875rem; font-weight: 700; color: #fff; text-decoration: none; }
        a.btn:hover { background: #014585; }
    </style>
</head>
<body>
    <div class="card">
        <p class="code">500</p>
        <span class="rule" aria-hidden="true"></span>
        <h1>Terjadi Kesalahan</h1>
        <p>Maaf, terjadi gangguan pada server kami. Tim kami akan segera memperbaikinya — silakan coba beberapa saat lagi.</p>
        <a class="btn" href="{{ url('/') }}">Kembali ke Beranda</a>
    </div>
</body>
</html>
