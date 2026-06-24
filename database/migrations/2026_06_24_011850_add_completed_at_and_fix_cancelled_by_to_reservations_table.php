<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('cancelled_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn('cancelled_by');
            $table->dropColumn('completed_at');
            $table->dropSoftDeletes();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('cancelled_by')->nullable()->after('cancellation_reason');
        });
    }
};
