<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Color;

return new class extends Migration
{
    public function up(): void
    {
        $basicColors = [
            ['name' => 'olive', 'hex_code' => '#4f4631'],
            ['name' => 'teal', 'hex_code' => '#314f4a'],
            ['name' => 'navy', 'hex_code' => '#31344f'],
            ['name' => 'black', 'hex_code' => '#000000'],
            ['name' => 'white', 'hex_code' => '#ffffff'],
            ['name' => 'red', 'hex_code' => '#ff0000'],
            ['name' => 'blue', 'hex_code' => '#0000ff'],
            ['name' => 'green', 'hex_code' => '#00ff00'],
            ['name' => 'yellow', 'hex_code' => '#ffff00'],
            ['name' => 'gray', 'hex_code' => '#808080'],
        ];

        $validColorIds = [];
        foreach ($basicColors as $c) {
            $color = Color::updateOrCreate(
                ['name' => strtolower($c['name'])],
                ['hex_code' => $c['hex_code']]
            );
            $validColorIds[] = $color->id;
        }

        // Delete any color that is not in the 10 basic colors
        Color::whereNotIn('id', $validColorIds)->delete();

        // Reassign colors to products randomly
        DB::table('color_product')->truncate();

        $products = DB::table('products')->pluck('id');
        foreach ($products as $productId) {
            $randKeys = array_rand($validColorIds, 2);
            DB::table('color_product')->insert([
                [
                    'product_id' => $productId,
                    'color_id' => $validColorIds[$randKeys[0]],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'product_id' => $productId,
                    'color_id' => $validColorIds[$randKeys[1]],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function down(): void
    {
        // 
    }
};
