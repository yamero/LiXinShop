<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCardRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(AddCardRequest $request)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) { // 购物车已存在该商品
            $cart->increment('amount', $amount);
        } else { // 购物车不存在该商品
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user->id);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return ['status' => 1, 'msg' => '已添加至购物车'];
    }
}
