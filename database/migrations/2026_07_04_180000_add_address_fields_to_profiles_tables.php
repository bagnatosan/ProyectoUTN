<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->string('street')->nullable()->after('address');
            $table->string('street_number', 20)->nullable()->after('street');
            $table->string('floor', 20)->nullable()->after('street_number');
            $table->string('apartment', 20)->nullable()->after('floor');
            $table->string('province', 100)->nullable()->after('apartment');
            $table->string('locality', 100)->nullable()->after('province');
            $table->string('postal_code', 10)->nullable()->after('locality');
        });

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('street')->nullable()->after('address');
            $table->string('street_number', 20)->nullable()->after('street');
            $table->string('floor', 20)->nullable()->after('street_number');
            $table->string('apartment', 20)->nullable()->after('floor');
            $table->string('province', 100)->nullable()->after('apartment');
            $table->string('locality', 100)->nullable()->after('province');
            $table->string('postal_code', 10)->nullable()->after('locality');
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['street', 'street_number', 'floor', 'apartment', 'province', 'locality', 'postal_code']);
        });

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['street', 'street_number', 'floor', 'apartment', 'province', 'locality', 'postal_code']);
        });
    }
};
