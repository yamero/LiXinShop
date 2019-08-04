<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /*
     * 提交订单
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

    /*
     * 确认收货
     */
    public function received(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收货
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return res(1, '成功确认收货');
    }

    public function review(Order $order)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        if ($order->ship_status != Order::SHIP_STATUS_RECEIVED) {
            throw new InvalidRequestException('该订单未确认收货，不可评价');
        }

        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        if ($order->ship_status != Order::SHIP_STATUS_RECEIVED) {
            throw new InvalidRequestException('该订单未确认收货，不可评价');
        }

        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }

        $reviews = $request->input('reviews');
        // 开启事务
        $order = DB::transaction(function () use ($reviews, $order) {

            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            // 将订单标记为已评价
            $order->update(['reviewed' => true]);

            return $order;

        });

        // 触发用户评价订单事件
        event(new OrderReviewed($order));

        return redirect()->back();
    }
}
