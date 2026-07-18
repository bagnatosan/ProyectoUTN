<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('mp_payment_id')->nullable()->after('payment_status');
            $table->string('mp_preference_id')->nullable()->after('mp_payment_id');
            $table->string('mp_status')->nullable()->after('mp_preference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable(false)->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropColumn(['mp_payment_id', 'mp_preference_id', 'mp_status']);
        });
    }
};
