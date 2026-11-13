<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('title', 60)->nullable()->after('level_name');
            $table->string('body', 300)->nullable()->after('title');
            $table->string('signer_name', 80)->nullable()->after('body');
            $table->string('signer_title', 60)->nullable()->after('signer_name');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['title', 'body', 'signer_name', 'signer_title']);
        });
    }
};
