<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'size', 'color', 'price', 'status', 'user_id'];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function orders(){
        return $this->belongsToMany(Orders::class);
    }

    public function carts(){
        return $this->belongsToMany(Cart::class);
    }

    public function cartsItems(){
        return $this->belongsToMany(CartItems::class);
    }

    public function orderItems(){
        return $this->belongsToMany(OrderItem::class);
    }
}
