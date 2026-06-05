<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ShopDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->command->info('Cleaning Category, Product, ProductVariant and ProductImage tables...');
        ProductImage::truncate();
        ProductVariant::truncate();
        Product::truncate();
        Category::truncate();

        Schema::enableForeignKeyConstraints();

        $jsonPath = database_path('data/shop_data.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("Can't find JSON file at: {$jsonPath}");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        // Pre-load color/size maps để tránh N+1 khi lookup
        $colorMap = Color::all()->keyBy(fn($c) => strtolower($c->name));
        $sizeMap  = Size::all()->keyBy(fn($s) => strtoupper($s->name));

        $this->command->info('Loading new shop data...');

        foreach ($data['categories'] as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name'        => $categoryData['name'],
                    'description' => $categoryData['description'] ?? null,
                ]
            );

            foreach ($categoryData['products'] as $productData) {
                // Mỗi product ra mắt ngẫu nhiên trong khoảng 3-12 tháng trước
                $productCreatedAt = Carbon::now()->subDays(rand(90, 365));

                $product = Product::updateOrCreate(
                    ['slug' => $productData['slug']],
                    [
                        'category_id'    => $category->id,
                        'name'           => $productData['name'],
                        'price'          => $productData['price'],
                        'price_discount' => $productData['price_discount'] ?? null,
                        'description'    => $productData['description'] ?? null,
                        'is_active'      => $productData['is_active'] ?? true,
                        'created_at'     => $productCreatedAt,
                        'updated_at'     => $productCreatedAt->copy()->addDays(rand(0, 5)),
                    ]
                );

                // Sync ProductVariants từ mảng [{color, size}]
                if (! empty($productData['variants'])) {
                    foreach ($productData['variants'] as $variantData) {
                        $color = isset($variantData['color'])
                            ? $colorMap->get(strtolower($variantData['color']))
                            : null;

                        $size = isset($variantData['size'])
                            ? $sizeMap->get(strtoupper($variantData['size']))
                            : null;

                        ProductVariant::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'color_id'   => $color?->id,
                                'size_id'    => $size?->id,
                            ],
                            [
                                'created_at' => $productCreatedAt,
                                'updated_at' => $productCreatedAt,
                            ]
                        );
                    }
                }

                // Loading Images
                if (! empty($productData['images'])) {
                    foreach ($productData['images'] as $index => $imgPath) {
                        ProductImage::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'img_path'   => $imgPath,
                            ],
                            [
                                'is_primary' => $index === 0,
                                'alt'        => $product->name,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Shop data loaded successfully!');
    }
}
