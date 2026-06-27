<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('bank_cbu')->nullable()->after('profit_margin');
            $table->string('bank_alias')->nullable()->after('bank_cbu');
            $table->string('bank_name')->nullable()->after('bank_alias');
            $table->string('bank_account_holder')->nullable()->after('bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_cbu', 'bank_alias', 'bank_name', 'bank_account_holder']);
        });
    }
};
