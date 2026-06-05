<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    private const STANDARD_ORDERS_PER_USER = 5;
    private const MIN_ORDERS_PER_PRODUCT   = 10;

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->command->info('Deleting old order data...');
        OrderItem::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::where('role', '!=', 'admin')->get();

        $products = Product::where('is_active', true)
            ->with(['variants.color', 'variants.size'])
            ->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found. Run ShopDataSeeder first!');
            return;
        }

        // ── Phase 1: Tạo lịch sử mua hàng chuẩn cho từng user ──────────────
        $this->command->info(
            'Phase 1: Creating ' . self::STANDARD_ORDERS_PER_USER . ' orders per user...'
        );

        foreach ($users as $user) {
            for ($i = 1; $i <= self::STANDARD_ORDERS_PER_USER; $i++) {
                $status = ($i <= self::STANDARD_ORDERS_PER_USER - 1)
                    ? OrderStatus::PAID
                    : OrderStatus::PENDING;

                // Order củ dần theo thường lệ: order 1 lâu nhất, order 5 gần nhất
                $daysAgo = rand(
                    (self::STANDARD_ORDERS_PER_USER - $i) * 10 + 5,
                    (self::STANDARD_ORDERS_PER_USER - $i) * 10 + 30
                );

                $this->createOrder(
                    $user->id,
                    $status,
                    $products->random(rand(1, 3))->all(),
                    Carbon::now()->subDays($daysAgo)
                );
            }
        }

        // ── Phase 2: Đảm bảo mỗi product có ít nhất MIN_ORDERS_PER_PRODUCT ─
        $this->command->info(
            'Phase 2: Guaranteeing ' . self::MIN_ORDERS_PER_PRODUCT . ' orders per product...'
        );

        foreach ($products as $product) {
            $existing = OrderItem::where('product_id', $product->id)->count();
            $needed   = self::MIN_ORDERS_PER_PRODUCT - $existing;

            for ($i = 0; $i < $needed; $i++) {
                $randomUser = $users->random();
                $this->createOrder(
                    $randomUser->id,
                    OrderStatus::PAID,
                    [$product],
                    Carbon::now()->subDays(rand(1, 90))
                );
            }
        }

        $this->command->info(
            'Order seeding done! '
            . Order::count() . ' orders, '
            . OrderItem::count() . ' order items.'
        );
    }

    /**
     * Tạo một Order + OrderItems cho danh sách products cho trước.
     *
     * @param int         $userId
     * @param OrderStatus $status
     * @param array<\App\Models\Product> $productList
     * @param Carbon|null $createdAt
     */
    private function createOrder(
        int $userId,
        OrderStatus $status,
        array $productList,
        ?Carbon $createdAt = null
    ): Order {
        $createdAt ??= Carbon::now()->subDays(rand(1, 90));

        $order = Order::create([
            'user_id'      => $userId,
            'status'       => $status,
            'totalAmount'  => 0,
            'delivery_fee' => 0,
            'discount'     => 0,
            'created_at'   => $createdAt,
            'updated_at'   => $createdAt,
        ]);

        $total = 0.0;

        foreach ($productList as $product) {
            $quantity    = rand(1, 3);
            $price       = (float) ($product->price_discount ?? $product->price);
            $totalMoney  = $price * $quantity;
            $total      += $totalMoney;

            $variant     = $product->variants->isNotEmpty()
                ? $product->variants->random()
                : null;

            OrderItem::create([
                'order_id'             => $order->id,
                'product_id'           => $product->id,
                'product_variant_id'   => $variant?->id,
                'product_name'         => $product->name,
                'product_variant_name' => $variant?->variant_name,
                'quantity'             => $quantity,
                'price'                => $price,
                'totalMoney'           => $totalMoney,
                'created_at'           => $createdAt,
                'updated_at'           => $createdAt,
            ]);
        }

        $order->update([
            'totalAmount' => $total,
            'updated_at'  => $createdAt,
        ]);

        return $order;
    }
}
