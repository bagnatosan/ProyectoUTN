<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Costo de envío configurable por cada vendedor
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('bank_account_holder');
        });

        // Modalidad de entrega por reserva
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('delivery_type', ['delivery', 'pickup'])->default('pickup')->after('quantity');
            $table->string('shipping_address')->nullable()->after('delivery_type');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_address');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'shipping_address', 'shipping_cost']);
        });
    }
};
