<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * v22: filial obunasi. `subscription_ends_on` — obuna tugaydigan kun (shu kundan boshlab filial faqat-ko'rish rejimida).
 * `subscription_exempt` — obunasiz (doim faol) filial (sAdmin belgilaydi). Mavjud filiallarga 30 kun bepul beriladi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $t) {
            if (! Schema::hasColumn('branches', 'subscription_ends_on')) {
                $t->date('subscription_ends_on')->nullable()->after('closed_reason');
            }
            if (! Schema::hasColumn('branches', 'subscription_exempt')) {
                $t->boolean('subscription_exempt')->default(false)->after('subscription_ends_on');
            }
        });

        DB::table('branches')->whereNull('subscription_ends_on')->update(['subscription_ends_on' => now()->addDays(30)->toDateString()]);

        if (! Schema::hasTable('subscription_requests')) {
            Schema::create('subscription_requests', function (Blueprint $t) {
                $t->id();
                $t->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $t->string('plan', 10);                       // monthly | yearly
                $t->unsignedBigInteger('amount');             // so'rov paytidagi narx (o'zgarmaydi)
                $t->string('receipt_path', 255);              // private disk
                $t->string('status', 10)->default('pending'); // pending | approved | rejected
                $t->text('note')->nullable();
                $t->string('reject_reason', 500)->nullable();
                $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamp('reviewed_at')->nullable();
                $t->date('new_ends_on')->nullable();
                $t->timestamps();
                $t->index(['status', 'created_at']);
                $t->index(['branch_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_requests');
        Schema::table('branches', function (Blueprint $t) {
            $t->dropColumn(['subscription_ends_on', 'subscription_exempt']);
        });
    }
};
