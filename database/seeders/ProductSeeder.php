<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        // Base product types
        $baseProducts = [
            'Áo Thun',
            'Áo Sơ Mi',
            'Quần Jeans',
            'Quần Short',
            'Váy',
            'Đầm',
            'Áo Khoác',
            'Áo Hoodie',
            'Áo Blazer',
            'Quần Tây',
            'Áo Len',
            'Áo Polo',
            'Set Bộ',
            'Đồ Thể Thao',
            'Đồ Ngủ',
            'Áo Tanktop',
            'Áo Cardigan',
            'Quần Jogger',
            'Chân Váy',
            'Áo Croptop'
        ];

        $adjectives = [
            'Basic',
            'Cao Cấp',
            'Premium',
            'Slim Fit',
            'Oversize',
            'Form Rộng',
            'Hàn Quốc',
            'Unisex',
            'Vintage',
            'Hiện Đại',
            'Thanh Lịch',
            'Trẻ Trung',
            'Năng Động',
            'Thời Trang',
            'Mùa Hè',
            'Mùa Đông'
        ];

        $materials = [
            'Cotton',
            'Lụa',
            'Denim',
            'Polyester',
            'Len',
            'Thun Lạnh',
            'Kaki',
            'Voan'
        ];

        for ($i = 1; $i <= 100; $i++) {

            $productName = $this->generateName("R-{$i}", $baseProducts, $adjectives, $materials);
            $slug = Str::slug($productName);

            Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $productName,
                    'price' => rand(100000, 900000),
                    'price_discount' => rand(50000, 500000),
                    'description' => "Sản phẩm {$productName} chất lượng cao.",
                    'category_id' => $categories->random()->id,
                ]
            );
        }
    }

    private function generateName($index, $baseProducts, $adjectives, $materials): string
    {
        $base = collect($baseProducts)->random();
        $adj = collect($adjectives)->random();
        $material = collect($materials)->random();
        return "{$base} {$adj} {$material} {$index}";
    }
}
