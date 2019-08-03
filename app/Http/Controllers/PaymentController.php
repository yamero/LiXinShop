<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /*
     * 跳转到支付宝支付
     */
    public function alipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        // 订单已支付或已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态异常');
        }

        // 跳转到支付宝
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => "LiXin Shop订单付款：{$order->no}", // 订单标题
        ]);
    }

    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '支付异常']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    public function alipayNotify()
    {
        // 校验输入参数
        $data  = app('alipay')->verify();

        // 所有交易状态：https://docs.open.alipay.com/59/103672
        // 交易没有成功，返回
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }

        // 根据返回的订单号查询订单
        $order = Order::where('no', $data->out_trade_no)->first();

        // 订单不存在，返回
        if (!$order) {
            return 'fail';
        }

        // 订单已支付，返回
        if ($order->paid_at) {
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        return app('alipay')->success();
    }
}
