<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:cleanup-unverified-orders')]
#[Description('Clean up guest orders that remain unverified after 24 hours')]
class CleanupUnverifiedOrders extends Command
{
    public function handle(): int
    {
        $orders = Order::where('status', OrderStatus::NOT_VERIFY)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        $count = $orders->count();

        foreach ($orders as $order) {
            $order->orderItems()->delete();
            $order->delete();
        }

        $this->info("Cleaned up {$count} unverified orders.");

        return self::SUCCESS;
    }
}
