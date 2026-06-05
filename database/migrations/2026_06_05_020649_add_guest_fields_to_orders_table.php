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
        Schema::table('orders', function (Blueprint $table) {
            // Allow guest orders (no authenticated user)
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Guest identification fields
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->text('guest_address')->nullable()->after('guest_email');

            // Pricing breakdown fields
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('totalAmount');
            $table->decimal('discount', 10, 2)->default(0)->after('delivery_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_email',
                'guest_address',
                'delivery_fee',
                'discount',
            ]);

            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
