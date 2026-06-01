<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Color;
use App\Models\Size;

return new class extends Migration
{
    public function up(): void
    {
        // First insert default colors and sizes
        $defaultColors = [
            ['name' => 'olive', 'hex_code' => '#4f4631'],
            ['name' => 'teal', 'hex_code' => '#314f4a'],
            ['name' => 'navy', 'hex_code' => '#31344f'],
            ['name' => 'black', 'hex_code' => '#000000'],
            ['name' => 'white', 'hex_code' => '#ffffff'],
            ['name' => 'red', 'hex_code' => '#ff0000'],
            ['name' => 'blue', 'hex_code' => '#0000ff'],
            ['name' => 'green', 'hex_code' => '#00ff00'],
        ];

        foreach ($defaultColors as $c) {
            Color::firstOrCreate(['name' => strtolower($c['name'])], ['hex_code' => $c['hex_code']]);
        }

        $defaultSizes = [
            ['name' => 'XS', 'label' => 'X-Small'],
            ['name' => 'S', 'label' => 'Small'],
            ['name' => 'M', 'label' => 'Medium'],
            ['name' => 'L', 'label' => 'Large'],
            ['name' => 'XL', 'label' => 'X-Large'],
            ['name' => 'XXL', 'label' => 'XX-Large'],
            ['name' => 'XXXL', 'label' => 'XXX-Large'],
        ];

        foreach ($defaultSizes as $s) {
            Size::firstOrCreate(['name' => strtoupper($s['name'])], ['label' => $s['label']]);
        }

        // Migrate product data
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $colors = json_decode($product->colors ?? '[]', true) ?? [];
            $sizes = json_decode($product->sizes ?? '[]', true) ?? [];

            foreach ($colors as $colorName) {
                // Handle case where product might have color not in defaults
                $color = Color::firstOrCreate(['name' => strtolower($colorName)], ['hex_code' => '#000000']);
                DB::table('color_product')->insertOrIgnore([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($sizes as $sizeName) {
                $size = Size::firstOrCreate(['name' => strtoupper($sizeName)], ['label' => strtoupper($sizeName)]);
                DB::table('product_size')->insertOrIgnore([
                    'product_id' => $product->id,
                    'size_id' => $size->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('color_product')->truncate();
        DB::table('product_size')->truncate();
    }
};
