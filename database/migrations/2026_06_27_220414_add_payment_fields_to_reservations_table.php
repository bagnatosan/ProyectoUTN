<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('payment_status', ['pending_upload', 'uploaded', 'confirmed'])
                  ->nullable()
                  ->default(null)
                  ->after('status');
            $table->decimal('transfer_amount', 10, 2)->nullable()->after('payment_status');
            $table->date('transfer_date')->nullable()->after('transfer_amount');
            $table->string('transfer_reference')->nullable()->after('transfer_date');
            $table->string('receipt_path')->nullable()->after('transfer_reference');
            $table->timestamp('payment_confirmed_at')->nullable()->after('receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'transfer_amount',
                'transfer_date',
                'transfer_reference',
                'receipt_path',
                'payment_confirmed_at',
            ]);
        });
    }
};
