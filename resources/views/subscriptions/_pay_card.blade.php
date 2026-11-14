{{-- v27: 3D bank kartasi (to'lov rekvizitlari). Ota Alpine ko'lamidagi `copied` va `copyCard()` ishlatiladi. --}}
<div class="bx-ccwrap">
    <div class="bx-cc" data-bxtilt data-bxtilt-max="9">
        <div class="bx-cc-idle">
            <div class="bx-cc-bg"></div>
            <div class="bx-cc-body">
                <div class="bx-cc-top" style="transform: translateZ(26px)">
                    <span class="bx-chip" aria-hidden="true"><i></i><i></i><i></i></span>
                    <span class="bx-cc-tag">KARTA</span>
                </div>
                <div class="bx-cc-num select-all" style="transform: translateZ(40px)">{{ $cardPretty }}</div>
                <div class="bx-cc-bot" style="transform: translateZ(28px)">
                    <div class="min-w-0">
                        <div class="bx-cc-cap">Karta egasi</div>
                        <div class="bx-cc-holder">{{ $holder ?: $branchName }}</div>
                    </div>
                    <div class="bx-cc-pulse" aria-hidden="true"><span></span><span></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="bx-copy" :class="{ 'is-done': copied }" @click="copyCard()">
    <span class="bx-copy-ic" aria-hidden="true">
        <x-icon name="copy" class="h-4 w-4 bx-ic-copy" />
        <x-icon name="check" class="h-4 w-4 bx-ic-check" />
    </span>
    <span x-text="copied ? 'Karta raqami nusxalandi' : 'Karta raqamini nusxalash'">Karta raqamini nusxalash</span>
</button>
