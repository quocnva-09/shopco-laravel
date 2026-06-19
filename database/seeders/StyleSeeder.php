<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Style;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $styles = ['casual', 'formal', 'party', 'gym'];

        foreach ($styles as $style) {
            Style::updateOrCreate(
                ['name' => ucfirst($style)],
                ['slug' => Str::slug($style)]
            );
        }
    }
}
