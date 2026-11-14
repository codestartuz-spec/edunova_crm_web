<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * v22: bir filial uchun bir vaqtda faqat BITTA kutayotgan chek bo'lishi bazaning o'zida kafolatlanadi
 * (`pending_branch_id` unikal; tasdiqlangan/rad etilganda NULL). Ikki tez bosish yoki ikki oyna poygasini yopadi.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('subscription_requests', 'pending_branch_id')) {
            Schema::table('subscription_requests', function (Blueprint $t) {
                $t->unsignedBigInteger('pending_branch_id')->nullable()->unique();
            });
        }

        // Mavjud kutayotgan cheklar: har filialdan eng eskisi qoladi, qolganlari (agar bo'lsa) rad etilmaydi, faqat belgisiz qoladi
        $seen = [];
        foreach (DB::table('subscription_requests')->where('status', 'pending')->orderBy('id')->get(['id', 'branch_id']) as $r) {
            if (isset($seen[$r->branch_id])) {
                continue;
            }
            $seen[$r->branch_id] = true;
            DB::table('subscription_requests')->where('id', $r->id)->update(['pending_branch_id' => $r->branch_id]);
        }
    }

    public function down(): void
    {
        Schema::table('subscription_requests', function (Blueprint $t) {
            $t->dropUnique(['pending_branch_id']);
            $t->dropColumn('pending_branch_id');
        });
    }
};
