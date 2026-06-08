<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Alter price_discount column from DECIMAL(12,2) — monetary value —
     * to UNSIGNED TINYINT (0–99) — discount percentage.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('price_discount')
                ->nullable()
                ->default(null)
                ->comment('Discount percentage 0-99')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_discount', 12, 2)
                ->nullable()
                ->default(0)
                ->change();
        });
    }
};

