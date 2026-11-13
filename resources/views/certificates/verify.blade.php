<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Sertifikatni tekshirish · {{ \App\Support\Brand::short() }}</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:system-ui,sans-serif;background:#f8fafc;color:#0f172a;padding:20px}
        .c{width:100%;max-width:440px;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:28px;text-align:center}
        .ok{color:#059669}.bad{color:#dc2626}.i{font-size:52px}
        h1{font-size:20px;margin:8px 0 18px} dl{text-align:left;margin:0} dt{font-size:12px;color:#64748b;margin-top:12px} dd{margin:2px 0 0;font-weight:600}
    </style>
</head>
<body>
<div class="c">
@if (! $c)
    <div class="i bad">✖</div><h1 class="bad">Sertifikat topilmadi</h1>
    <p style="color:#64748b">Kod noto'g'ri yoki bunday sertifikat berilmagan.</p>
@elseif ($c->isRevoked())
    <div class="i bad">⚠</div><h1 class="bad">Bekor qilingan</h1>
    <p style="color:#64748b">Bu sertifikat endi amal qilmaydi.</p>
    <dl><dt>Raqam</dt><dd>{{ $c->number }}</dd></dl>
@else
    <div class="i ok">✔</div><h1 class="ok">Sertifikat haqiqiy</h1>
    <dl>
        <dt>Egasi</dt><dd>{{ $c->student_name }}</dd>
        <dt>Kurs</dt><dd>{{ $c->course_name }}@if ($c->level_name) — {{ $c->level_name }}@endif</dd>
        <dt>Berilgan sana</dt><dd>{{ $c->issued_on->format('d.m.Y') }}</dd>
        <dt>Filial</dt><dd>{{ $branch?->name }}</dd>
        <dt>Sertifikat raqami</dt><dd>{{ $c->number }}</dd>
    </dl>
@endif
</div>
</body>
</html>
