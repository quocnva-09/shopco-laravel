<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'price' => $this->product->price,
                    'price_discount' => $this->product->price_discount,
                    'images' => $this->product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                        ];
                    }),
                ];
            }),
        ];
    }
}
