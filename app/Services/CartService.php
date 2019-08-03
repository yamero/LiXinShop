<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();

        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) { // 购物车已存在该商品
            $cart->increment('amount', $amount);
        } else { // 购物车不存在该商品
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user->id);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return res(1, '已添加至购物车');
    }

    public function remove($skuIds)
    {
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

        return res(1, '移除成功');
    }
}