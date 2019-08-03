<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /*
     * 创建订单
     */
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        $order = DB::transaction(function () use ($user, $request) {

            // 获取用户选择的收货地址，并更新收货地址的最后一次使用时间
            $address = UserAddress::find($request->input('address_id'));
            $address->update(['last_used_at' => Carbon::now()]);

            // 创建订单
            $order   = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            $order->user()->associate($user->id);
            $order->save();

            $totalAmount = 0;
            $items = $request->input('items');
            foreach ($items as $data) {

                $sku  = ProductSku::find($data['sku_id']);

                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);

                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku->id);
                $item->save();

                $totalAmount += $sku->price * $data['amount'];

                // 减库存
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('有商品库存不足');
                }

            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;

        });

        return ['status' => 1, 'msg' => '订单提交成功'];
    }
}
