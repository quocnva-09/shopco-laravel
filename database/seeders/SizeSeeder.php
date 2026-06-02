<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'XS',   'label' => 'X-Small'],
            ['name' => 'S',    'label' => 'Small'],
            ['name' => 'M',    'label' => 'Medium'],
            ['name' => 'L',    'label' => 'Large'],
            ['name' => 'XL',   'label' => 'X-Large'],
            ['name' => 'XXL',  'label' => 'XX-Large'],
            ['name' => 'XXXL', 'label' => 'XXX-Large'],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(
                ['name' => strtoupper($size['name'])],
                ['label' => $size['label']]
            );
        }

        $this->command->info('Sizes seeded successfully!');
    }
}
