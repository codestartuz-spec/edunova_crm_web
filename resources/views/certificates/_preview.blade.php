@php $gold = '#b8923a'; @endphp
{{-- Sertifikat PDF'ining jonli nusxasi: 842x595 birlik, konteynerga qarab masshtablanadi (certificates/pdf.blade.php bilan bir xil joylashuv). --}}
<style>
    #certCanvas{position:absolute;left:0;top:0;width:842px;height:595px;transform-origin:0 0;background:#fff;color:#1f2937;font-family:'DejaVu Sans','Plus Jakarta Sans Variable',system-ui,sans-serif;box-shadow:0 10px 40px -12px rgb(30 20 80 / .35);overflow:hidden}
    #certCanvas .a{position:absolute}
    #certCanvas .c{text-align:center}
    #certCanvas .ph{opacity:.38}
    #certCanvas img{display:inline-block}
    #certCanvas h1{margin:0;font-size:38px;letter-spacing:12px;font-weight:700;line-height:1.1;white-space:nowrap;overflow:hidden;text-overflow:clip}
    #certCanvas .fadein{animation:certPop .35s ease both}
    @keyframes certPop{from{opacity:.2;transform:translateY(2px)}to{opacity:1;transform:none}}
</style>
<div id="certCanvas" data-accent="{{ $accent }}">
    <div class="a" style="left:16px;top:16px;width:810px;height:8px;background:{{ $accent }}"></div>
    <div class="a" style="left:16px;top:16px;width:804px;height:557px;border:3px solid {{ $accent }};box-sizing:content-box"></div>
    <div class="a" style="left:25px;top:25px;width:790px;height:541px;border:.75px solid {{ $gold }};box-sizing:content-box"></div>
    <div class="a" style="left:34px;top:34px;width:34px;height:34px;border-top:3px solid {{ $gold }};border-left:3px solid {{ $gold }}"></div>
    <div class="a" style="right:34px;top:34px;width:34px;height:34px;border-top:3px solid {{ $gold }};border-right:3px solid {{ $gold }}"></div>
    <div class="a" style="left:34px;bottom:34px;width:34px;height:34px;border-bottom:3px solid {{ $gold }};border-left:3px solid {{ $gold }}"></div>
    <div class="a" style="right:34px;bottom:34px;width:34px;height:34px;border-bottom:3px solid {{ $gold }};border-right:3px solid {{ $gold }}"></div>

    <div class="a c" style="left:60px;right:60px;top:42px">
        <img id="pvLogo" src="{{ $logo ?: $fallbackLogo }}" data-fallback="{{ $logo ? '' : '1' }}" data-default="{{ $logo ?: $fallbackLogo }}" alt="" style="height:46px;max-width:240px;object-fit:contain;{{ ($logo ?: $fallbackLogo) ? '' : 'display:none' }}">
        <div style="font-size:9px;letter-spacing:3px;color:#6b7280;margin-top:4px">{{ mb_strtoupper($branchName ?: 'Filial nomi') }}</div>
    </div>

    <div class="a c" style="left:60px;right:60px;top:112px">
        <h1 id="pvTitle" style="color:{{ $accent }}">{{ $v['title'] }}</h1>
        <div style="font-size:9px;letter-spacing:4px;color:{{ $gold }};margin-top:2px">MUVAFFAQIYAT SERTIFIKATI</div>
        <div style="width:120px;border-top:1.5px solid {{ $gold }};margin:10px auto 0"></div>
    </div>

    <div class="a c" style="left:60px;right:60px;top:186px">
        <div style="font-size:9px;letter-spacing:3px;color:#6b7280">SERTIFIKAT EGASI</div>
        <div style="font-size:32px;font-weight:700;margin:8px 50px 0;padding-bottom:8px;border-bottom:.75px solid #d1d5db">Aliyev Valijon</div>
        <div style="font-size:17px;font-weight:700;color:{{ $accent }};margin-top:14px">Koreys tili · TOPIK 4</div>
        <div id="pvBody" style="font-size:11px;color:#4b5563;line-height:1.5;margin:8px 90px 0"></div>
    </div>

    <div class="a" style="left:62px;right:62px;top:402px;height:128px">
        <table style="width:100%;border-collapse:collapse;height:100%"><tr>
            <td style="width:34%;text-align:center;vertical-align:bottom">
                <img id="pvSig" src="{{ $signature }}" data-default="{{ $signature }}" alt="" style="height:42px;max-width:170px;object-fit:contain;{{ $signature ? '' : 'display:none' }}"><br>
                <div style="border-top:.75px solid #9ca3af;padding-top:4px;width:180px;margin:0 auto;font-size:10px"><b id="pvSigner"></b><br><span id="pvSignerTitle" style="font-size:8px;color:#6b7280"></span></div>
            </td>
            <td style="width:32%;text-align:center;vertical-align:bottom">
                <div style="font-size:8px;color:#6b7280;letter-spacing:2px">BERILGAN SANA</div>
                <div style="font-size:12px;font-weight:700;margin-bottom:8px">{{ now()->format('d.m.Y') }}</div>
                <div style="font-size:8px;color:#6b7280;letter-spacing:2px">SERTIFIKAT RAQAMI</div>
                <div style="font-size:10px;font-weight:700">{{ now()->format('Y') }}-000001</div>
            </td>
            <td style="width:34%;text-align:center;vertical-align:bottom">
                <img src="{{ $sampleQr }}" alt="" style="width:74px;height:74px"><br>
                <span style="font-size:8px;color:#6b7280">Sertifikat haqiqiyligini tekshirish<br>uchun QR kodni skanerlang</span>
            </td>
        </tr></table>
    </div>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var canvas = $('certCanvas'), fit = $('certFit'), form = $('certForm');
    if (!canvas || !fit || !form) return;

    function scale() { canvas.style.transform = 'scale(' + (fit.clientWidth / 842) + ')'; }
    scale();
    window.addEventListener('resize', scale);
    if (window.ResizeObserver) new ResizeObserver(scale).observe(fit);

    function pop(el) { el.classList.remove('fadein'); void el.offsetWidth; el.classList.add('fadein'); }
    function text(el, val, placeholder) {
        var v = (val || '').trim();
        el.textContent = v || placeholder;
        el.classList.toggle('ph', !v);
    }
    function sync(which) {
        var f = form.elements;
        var t = $('pvTitle'), b = $('pvBody');
        if (!which || which === 'title') {
            t.textContent = f.title.value || 'SERTIFIKAT';
            var sz = 38; t.style.fontSize = sz + 'px'; t.style.letterSpacing = '12px';
            while (t.scrollWidth > t.clientWidth && sz > 14) { sz -= 1; t.style.fontSize = sz + 'px'; t.style.letterSpacing = (sz * 0.32) + 'px'; }
            if (which) pop(t);
        }
        if (!which || which === 'body') {
            var v = (f.body.value || '').trim();
            b.textContent = v ? v.charAt(0).toUpperCase() + v.slice(1) : 'Matn shu yerda chiqadi';
            b.classList.toggle('ph', !v);
            $('bodyCount').textContent = f.body.value.length;
            if (which) pop(b);
        }
        if (!which || which === 'signer_name') { text($('pvSigner'), f.signer_name.value, 'F.I.O'); if (which) pop($('pvSigner')); }
        if (!which || which === 'signer_title') { text($('pvSignerTitle'), f.signer_title.value, 'Lavozim'); if (which) pop($('pvSignerTitle')); }
    }
    sync();

    ['title', 'body', 'signer_name', 'signer_title'].forEach(function (n) {
        form.elements[n].addEventListener('input', function () { sync(n); dirty(); });
    });

    var urls = {};
    function image(inputName, imgId) {
        var input = form.elements[inputName], img = $(imgId);
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (urls[inputName]) { URL.revokeObjectURL(urls[inputName]); urls[inputName] = null; }
            if (file && /^image\//.test(file.type)) {
                urls[inputName] = URL.createObjectURL(file);
                img.src = urls[inputName];
                img.style.display = '';
            } else {
                img.src = img.dataset.default || '';
                img.style.display = img.dataset.default ? '' : 'none';
            }
            pop(img);
            dirty();
        });
    }
    image('logo', 'pvLogo');
    image('signature', 'pvSig');

    var d = $('certDirty');
    function dirty() { if (d) d.classList.remove('hidden'); }
    form.addEventListener('submit', function () { if (d) d.classList.add('hidden'); });
})();
</script>
