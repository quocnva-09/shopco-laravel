<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Allow guest reviews (no authenticated user)
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Guest identification fields
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop guest columns if they exist
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'guest_email')) {
                $table->dropColumn('guest_email');
            }
        });

        // Remove guest reviews (NULL user_id) before making column NOT NULL
        DB::table('reviews')->whereNull('user_id')->delete();

        // Safely drop the foreign key if it exists
        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            // Foreign key may already be dropped, ignore
        }

        // Restore user_id to NOT NULL and re-add foreign key
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
