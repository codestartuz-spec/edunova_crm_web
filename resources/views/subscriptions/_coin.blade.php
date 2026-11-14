{{-- v27: CSS 3D tanga (qalinligi qatlamlardan, aylanadi). Faqat bezak. --}}
<div class="bx-coin" style="--s: {{ $s ?? 96 }}px; animation-duration: {{ $dur ?? 8 }}s">
    @foreach ([-5, -3, -1, 1, 3, 5] as $k)<i style="--k: {{ $k }}"></i>@endforeach
    <i class="f" style="--k: 7">UZS</i>
    <i class="b">UZS</i>
</div>
