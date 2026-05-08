<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Áo Thun Nam',
            'Áo Thun Nữ',
            'Áo Sơ Mi Nam',
            'Áo Sơ Mi Nữ',
            'Quần Jeans Nam',
            'Quần Jeans Nữ',
            'Quần Short',
            'Váy Đầm',
            'Áo Khoác',
            'Áo Hoodie',
            'Áo Blazer',
            'Quần Tây',
            'Đồ Thể Thao',
            'Đồ Ngủ',
            'Đồ Lót',
            'Áo Len',
            'Áo Polo',
            'Set Bộ',
            'Đồ Công Sở',
            'Phụ Kiện Thời Trang',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => "Danh mục {$categoryName}",
                ]
            );
        }
    }
}
