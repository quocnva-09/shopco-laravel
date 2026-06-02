<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'olive',  'hex_code' => '#4f4631'],
            ['name' => 'teal',   'hex_code' => '#314f4a'],
            ['name' => 'navy',   'hex_code' => '#31344f'],
            ['name' => 'black',  'hex_code' => '#000000'],
            ['name' => 'white',  'hex_code' => '#ffffff'],
            ['name' => 'red',    'hex_code' => '#ff0000'],
            ['name' => 'blue',   'hex_code' => '#0000ff'],
            ['name' => 'green',  'hex_code' => '#00ff00'],
            ['name' => 'yellow', 'hex_code' => '#ffff00'],
            ['name' => 'gray',   'hex_code' => '#808080'],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['name' => strtolower($color['name'])],
                ['hex_code' => $color['hex_code']]
            );
        }

        $this->command->info('Colors seeded successfully!');
    }
}
