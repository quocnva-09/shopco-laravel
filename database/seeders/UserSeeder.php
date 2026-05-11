<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // user with real mail
        User::factory()->create([
            'name' => 'Kenji',
            'email' => env('MAIL_REAL_MAIL'),
            'password' => 'password',
            'role' => 'user',
        ]);

        // random 10 users
        User::factory(10)->create();
    }
}
