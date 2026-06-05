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
        // Remove options column from cart items
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'options')) {
                $table->dropColumn('options');
            }
            
            // Add product_variant_id
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();
        });
        
        // Remove options column from order items
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'options')) {
                $table->dropColumn('options');
            }
            
            // Add product_variant_id
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();

            // Add product_name
            $table->string('product_name')->nullable()->after('product_variant_id');
            // Add product_variant_name
            $table->string('product_variant_name')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // --- cart_items ---
        // Step 1: Drop foreign key first
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
        });

        // Step 2: Drop column and restore options
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('product_variant_id');
            $table->json('options')->nullable();
        });

        // --- order_items ---
        // Step 1: Drop foreign key first
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
        });

        // Step 2: Drop columns and restore options
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_variant_id', 'product_name', 'product_variant_name']);
            $table->json('options')->nullable();
        });
    }
};
