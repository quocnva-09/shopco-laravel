<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userJsonPath = database_path('data/user_data.json');

        if (File::exists($userJsonPath)) {
            $this->command->info('Creating users data...');

            $userData = json_decode(File::get($userJsonPath), true);

            foreach ($userData['users'] as $user) {
                User::updateOrCreate(
                    ['email' => $user['email']],
                    [
                        'name' => $user['name'],
                        'password' => Hash::make($user['password']),
                        'role' => $user['role'] ?? 'user',
                        'profile_image' => $user['profile_image'] ?? null,
                        'address' => $user['address'] ?? null,
                        'phone' => $user['phone'] ?? null,
                        'bio' => $user['bio'] ?? null,
                        'email_verified_at' => now(),
                    ]
                );
            }
            $this->command->info('Creating users data success!');
        } else {
            $this->command->warn("Creating users data failed: {$userJsonPath} not found");
        }
    }
}
