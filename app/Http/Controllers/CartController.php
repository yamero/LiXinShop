<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCardRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /*
     * 购物车列表
     */
    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderByDesc('last_used_at')->get();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'addresses' => $addresses
        ]);
    }

    /*
     * 添加商品到购物车
     */
    public function add(AddCardRequest $request)
    {
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        return $this->cartService->add($skuId, $amount);
    }

    /*
     * 将商品从购物车中移除
     */
    public function remove(ProductSku $sku, Request $request)
    {
        return $this->cartService->remove($sku->id);
    }
}
