<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->command->info('Deleting old data...');
        OrderItem::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::where('role', '!=', 'admin')->get();

        $products = Product::where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found in DB. Please run ShopDataSeeder first!');
            return;
        }

        $this->command->info('Creating 5 orders (4 Paid, 1 Pending) for each User...');

        foreach ($users as $user) {
            for ($i = 1; $i <= 5; $i++) {
                $status = ($i <= 4) ? 'paid' : 'pending';

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => $status,
                    'totalAmount' => 0,
                ]);

                $randomProducts = $products->random(rand(1, 3));
                $orderTotalAmount = 0;

                foreach ($randomProducts as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->price_discount ?? $product->price;
                    $totalMoney = $price * $quantity;
                    $orderTotalAmount += $totalMoney;

                    $selectedSize = !empty($product->sizes) ? $product->sizes[array_rand($product->sizes)] : null;
                    $selectedColor = !empty($product->colors) ? $product->colors[array_rand($product->colors)] : null;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'totalMoney' => $totalMoney,
                        'options' => json_encode([
                            'size' => $selectedSize,
                            'color' => $selectedColor
                        ]),
                    ]);
                }

                $order->update([
                    'totalAmount' => $orderTotalAmount
                ]);
            }
        }

        $this->command->info('Order Seeder completed!');
    }
}
