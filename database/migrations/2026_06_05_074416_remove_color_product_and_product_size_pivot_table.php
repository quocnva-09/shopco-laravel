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
        // Remove color_product pivot table
        Schema::dropIfExists('color_product');
        
        // Remove product_size pivot table
        Schema::dropIfExists('product_size');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create color_product pivot table
        Schema::create('color_product', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            $table->primary(['product_id', 'color_id']);
        });
        
        // Create product_size pivot table
        Schema::create('product_size', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->primary(['product_id', 'size_id']);
        });
    }
};
