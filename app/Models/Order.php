<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'status',
        'totalAmount',
        'delivery_fee',
        'discount',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
