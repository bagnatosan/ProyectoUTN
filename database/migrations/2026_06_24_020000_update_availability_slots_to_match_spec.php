<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('availability_slots', 'user_id')) {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('availability_slots', 'day_of_week')) {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->unsignedTinyInteger('day_of_week')->nullable()->after('end_time');
            });
        }

        if (Schema::hasColumn('availability_slots', 'business_profile_id')) {
            DB::statement('
                UPDATE availability_slots slot
                JOIN business_profiles bp ON bp.id = slot.business_profile_id
                SET slot.user_id = bp.user_id
                WHERE slot.user_id IS NULL
            ');
        }

        if (Schema::hasColumn('availability_slots', 'weekday')) {
            $dayMap = [
                'Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
                'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6,
            ];
            foreach ($dayMap as $dayName => $dayNum) {
                DB::statement("UPDATE availability_slots SET day_of_week = ? WHERE weekday = ?", [$dayNum, $dayName]);
            }
        }

        DB::statement('UPDATE availability_slots SET day_of_week = 1 WHERE day_of_week IS NULL');

        DB::statement('UPDATE availability_slots SET user_id = (SELECT id FROM users WHERE role = ? ORDER BY id LIMIT 1) WHERE user_id = 0 OR user_id IS NULL', ['seller']);

        if (Schema::hasColumn('availability_slots', 'business_profile_id')) {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->dropForeign(['business_profile_id']);
                $table->dropColumn('business_profile_id');
                $table->dropColumn('weekday');
                $table->dropColumn('is_active');
            });
        }

        DB::statement('ALTER TABLE availability_slots MODIFY user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE availability_slots MODIFY day_of_week TINYINT UNSIGNED NOT NULL');

        try {
            DB::statement('ALTER TABLE availability_slots ADD CONSTRAINT availability_slots_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // FK may already exist
        }

        try {
            DB::statement('ALTER TABLE availability_slots ADD UNIQUE INDEX availability_slots_unique (user_id, day_of_week, start_time, end_time)');
        } catch (\Exception $e) {
            // Index may already exist
        }
    }

    public function down(): void
    {
    }
};
