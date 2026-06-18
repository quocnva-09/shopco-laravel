<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\GuestOrderServiceInterface;
use App\DTOs\Order\GuestCheckoutDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\GuestOrderOtpMail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GuestOrderService implements GuestOrderServiceInterface
{
    public const OTP_LENGTH = 6;
    public const OTP_TTL = 300; // 5 minutes
    public const OTP_CACHE_KEY_PREFIX = 'order_otp_';

    public function checkout(GuestCheckoutDTO $dto): Order
    {
        // 1. Load all required products at once (avoid N+1 queries)
        $productIds = array_column($dto->items, 'product_id');
        $productMap = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $orderItemsData = [];
        $subtotal       = 0.0;
        $now            = now();

        foreach ($dto->items as $item) {
            $productId = (int) $item['product_id'];

            // Guard: product must exist in the database
            if (! $productMap->has($productId)) {
                throw new UnprocessableEntityHttpException(
                    __('exception.guest_checkout_product_not_found', ['id' => $productId])
                );
            }

            $product = $productMap->get($productId);

            $price = $product->final_price;
            $quantity   = (int) $item['quantity'];
            $totalMoney = $price * $quantity;
            $subtotal  += $totalMoney;

            // Resolve product_variant_id (Mode 1 or Mode 2)
            $variantId   = $this->resolveVariantId($productId, $item);
            $variantName = null;

            if ($variantId !== null) {
                $variant     = ProductVariant::with(['color', 'size'])->find($variantId);
                $variantName = $variant?->variant_name;
            }

            $orderItemsData[] = [
                'product_id'           => $productId,
                'product_variant_id'   => $variantId,
                'product_name'         => $product->name,
                'product_variant_name' => $variantName,
                'quantity'             => $quantity,
                'price'                => $price,
                'totalMoney'           => $totalMoney,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        // Compute order total: subtotal + delivery_fee - discount
        $totalAmount = $subtotal + $dto->deliveryFee - $dto->discount;

        $order = DB::transaction(function () use ($dto, $totalAmount, $orderItemsData) {
            $order = Order::create([
                'user_id'      => null,
                'guest_name'   => $dto->guestName,
                'guest_phone'  => $dto->guestPhone,
                'guest_email'  => $dto->guestEmail,
                'guest_address' => $dto->guestAddress,
                'status'       => OrderStatus::NOT_VERIFY,
                'totalAmount'  => $totalAmount,
                'delivery_fee' => $dto->deliveryFee,
                'discount'     => $dto->discount,
            ]);

            $items = array_map(
                fn(array $item): array => array_merge($item, ['order_id' => $order->id]),
                $orderItemsData
            );

            OrderItem::insert($items);

            return $order->load('orderItems');
        });

        $otp = str_pad((string) random_int(0, (int) (10 ** self::OTP_LENGTH - 1)), self::OTP_LENGTH, '0', STR_PAD_LEFT);
        
        Cache::put(self::OTP_CACHE_KEY_PREFIX . $order->id, $otp, self::OTP_TTL);

        Mail::to($order->guest_email)->later(now()->addSeconds(30), new GuestOrderOtpMail($order, $otp));

        return $order;
    }

    /**
     * Resolve product_variant_id in two modes:
     *   Mode 1 — product_variant_id sent directly
     *   Mode 2 — color_id + size_id → lookup ProductVariant
     */
    private function resolveVariantId(int $productId, array $item): ?int
    {
        if (! empty($item['product_variant_id'])) {
            return (int) $item['product_variant_id'];
        }

        if (! empty($item['color_id']) || ! empty($item['size_id'])) {
            $variant = ProductVariant::where('product_id', $productId)
                ->where('color_id', $item['color_id'] ?? null)
                ->where('size_id', $item['size_id'] ?? null)
                ->first();

            return $variant?->id;
        }

        return null;
    }

    public function verifyOtp(int $orderId, string $otp): bool
    {
        $cacheKey = self::OTP_CACHE_KEY_PREFIX . $orderId;
        $cachedOtp = Cache::get($cacheKey);

        if (! $cachedOtp || (string) $cachedOtp !== $otp) {
            throw new BadRequestHttpException('Invalid or expired OTP.');
        }

        $order = Order::findOrFail($orderId);

        if ($order->status !== OrderStatus::NOT_VERIFY) {
            throw new BadRequestHttpException('Order is already verified or cancelled.');
        }

        $order->status = OrderStatus::PROCESSING;
        $order->save();

        Cache::forget($cacheKey);

        return true;
    }

    public function resendOtp(int $orderId): bool
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== OrderStatus::NOT_VERIFY) {
            throw new BadRequestHttpException('Order is already verified or cancelled.');
        }

        $otp = str_pad((string) random_int(0, (int) (10 ** self::OTP_LENGTH - 1)), self::OTP_LENGTH, '0', STR_PAD_LEFT);
        
        Cache::put(self::OTP_CACHE_KEY_PREFIX . $order->id, $otp, self::OTP_TTL);

        Mail::to($order->guest_email)->later(now()->addSeconds(5), new GuestOrderOtpMail($order, $otp));

        return true;
    }
}
