<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'totalMoney' => (float) $this->totalMoney,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
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
