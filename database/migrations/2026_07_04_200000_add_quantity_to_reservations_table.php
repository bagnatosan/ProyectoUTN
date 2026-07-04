<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Se agrega después de product_id, con valor por defecto 1
            // para no romper las reservas existentes.
            $table->unsignedSmallInteger('quantity')->default(1)->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
