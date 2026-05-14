<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::PAID,
            OrderStatus::CANCELLED,
        ];

        for ($i = 0; $i < 10; $i++) {
            Order::create([
                'user_id' => $users->random()->id,
                'status' => $statuses[array_rand($statuses)],
                'totalAmount' => rand(100, 10000) / 100,
            ]);
        }
    }
}
