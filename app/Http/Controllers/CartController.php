<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCardRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /*
     * 购物车列表
     */
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();

        return view('cart.index', [
            'cartItems' => $cartItems
        ]);
    }

    /*
     * 添加商品到购物车
     */
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

    /*
     * 将商品从购物车中移除
     */
    public function remove(ProductSku $sku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        return ['status' => 1, 'msg' => '移除成功'];
    }
}
