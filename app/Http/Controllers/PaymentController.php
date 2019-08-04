<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /*
     * 跳转到支付宝支付
     */
    public function alipay(Order $order, Request $request)
    {
        // 使用策略判断是否是当前登录用户的订单
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

    /*
     * 发起微信扫码支付（Native支付-模式二）
     * 参考文档：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_5
     */
    public function wechatPay(Order $order, Request $request)
    {
        // 使用策略判断是否是当前登录用户的订单
        $this->authorize('own', $order);

        // 订单已支付或已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态异常');
        }

        // 调用微信扫码支付，得到支付二维码信息
        $res = app('wechat_pay')->scan([
            'out_trade_no' => $order->no,  // 商户订单流水号，与支付宝 out_trade_no 一样
            'total_fee' => $order->total_amount * 100, // 金额单位是分。
            'body'      => "LiXin Shop订单付款：{$order->no}", // 订单描述
        ]);

        // 生成一个包含支付信息的二维码
        $qrCode = new QrCode($res->code_url);

        // 将生成的二维码图片数据以字符串形式输出，并带上相应的响应类型
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    // 支付宝同步通知
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '支付异常']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 支付宝异步通知
    public function alipayNotify()
    {
        // 校验通知的合法性，并获取通知数据
        $data  = app('alipay')->verify();

        // 参考文档：https://docs.open.alipay.com/59/103672
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

        // 更新订单信息
        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        // 触发订单支付成功事件
        $this->afterPaid($order);

        return app('alipay')->success();
    }

    /*
     * 微信支付异步通知（微信没有同步通知）
     */
    public function wechatNotify()
    {
        // 校验通知的合法性，并获取通知数据
        $data  = app('wechat_pay')->verify();

        // 参考文档：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
        // 交易没有成功，返回
        if ($data->return_code != 'SUCCESS' || $data->result_code != 'SUCCESS') {
            return app('wechat_pay')->success();
        }

        // 根据返回的订单号查询订单
        $order = Order::where('no', $data->out_trade_no)->first();

        // 订单不存在，返回
        if (!$order) {
            return 'fail';
        }

        // 订单已支付，返回
        if ($order->paid_at) {
            return app('wechat_pay')->success();
        }

        // 更新订单信息
        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'wechat', // 支付方式
            'payment_no'     => $data->transaction_id, // 微信订单号
        ]);

        // 触发订单支付成功事件
        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    public function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
