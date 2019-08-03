<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /*
     * 创建订单
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $remark = $request->input('remark');
        $items = $request->input('items');
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $items, $remark);
    }

    /*
     * 订单列表
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with([
            'items.product',
            'items.productSku'
        ])->orderByDesc('created_at')->paginate();

        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    /*
     * 订单详情
     */
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        return view('orders.show', [
            'order' => $order->load(['items.product', 'items.productSku'])
        ]);
    }
}
