<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * v14: Usluga 8 pog'onaga kengaydi (18-24 oy, 2-3, 3-5, 5-10, 10+ yil).
 * Eski 4-pog'ona («18 oydan keyin») summalari yangi 5-8 pog'onalarga nusxalanadi, shuning uchun
 * mavjud sozlamalar bo'yicha hisob-kitob o'zgarmaydi. Qotirilgan guruh summalariga tegilmaydi.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('branches')->whereNotNull('usluga_rates')->orderBy('id')->each(function ($b) {
            $rates = json_decode($b->usluga_rates, true);
            if (! is_array($rates) || ! isset($rates[4])) {
                return;
            }
            foreach ([5, 6, 7, 8] as $t) {
                $rates[$t] ??= $rates[4];
            }
            DB::table('branches')->where('id', $b->id)->update(['usluga_rates' => json_encode($rates)]);
        });
    }

    public function down(): void
    {
        // Ma'lumot saqlanadi: qo'shilgan pog'onalar qaytarilmaydi.
    }
};
