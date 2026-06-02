<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Size;
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

        // 2. Xóa sạch dữ liệu cũ (bao gồm pivot tables)
        $this->command->info('Cleaning Category, Product, ProductImage and pivot tables...');
        ProductImage::truncate();
        \DB::table('color_product')->truncate();
        \DB::table('product_size')->truncate();
        Product::truncate();
        Category::truncate();

        // 3. Bật lại khóa ngoại
        Schema::enableForeignKeyConstraints();

        $jsonPath = database_path('data/shop_data.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("Can't find JSON file at: {$jsonPath}");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        $this->command->info('Loading new shop data...');

        foreach ($data['categories'] as $categoryData) {
            // Loading Category
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'] ?? null,
                ]
            );

            // Loading Products
            foreach ($categoryData['products'] as $productData) {
                $product = Product::updateOrCreate(
                    ['slug' => $productData['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'price' => $productData['price'],
                        'price_discount' => $productData['price_discount'] ?? null,
                        'description' => $productData['description'] ?? null,
                        'is_active' => $productData['is_active'] ?? true,
                    ]
                );

                // Sync Colors
                if (!empty($productData['colors'])) {
                    $colorIds = [];
                    foreach ($productData['colors'] as $colorName) {
                        $color = Color::where('name', strtolower($colorName))->first();
                        if ($color) {
                            $colorIds[] = $color->id;
                        }
                    }
                    $product->colors()->sync($colorIds);
                }

                // Sync Sizes
                if (!empty($productData['sizes'])) {
                    $sizeIds = [];
                    foreach ($productData['sizes'] as $sizeName) {
                        $size = Size::where('name', strtoupper($sizeName))->first();
                        if ($size) {
                            $sizeIds[] = $size->id;
                        }
                    }
                    $product->sizes()->sync($sizeIds);
                }

                // Loading Images
                if (!empty($productData['images'])) {
                    foreach ($productData['images'] as $index => $imgPath) {
                        ProductImage::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'img_path' => $imgPath,
                            ],
                            [
                                'is_primary' => $index === 0,
                                'alt' => $product->name,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Shop data loaded successfully!');
    }
}
