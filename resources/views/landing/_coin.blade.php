{{-- 3D tanga (faqat bezak) --}}
<div class="coin3d" style="--s:{{ $s ?? 54 }}px" aria-hidden="true">
    <i class="cf" style="--zz:calc(var(--s) * .07)">$</i>
    @for ($k = 2; $k >= -2; $k--)<i class="ce" style="--zz:calc(var(--s) * {{ $k * 0.028 }})"></i>@endfor
    <i class="cb" style="--zz:calc(var(--s) * -.07)">$</i>
</div>
