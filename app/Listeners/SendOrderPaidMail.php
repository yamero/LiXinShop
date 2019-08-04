<?php
/**
 * 监听订单支付成功事件
 */

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderPaidMail implements ShouldQueue
{
    public $tries = 3; // 失败后最大尝试执行次数

    public function handle(OrderPaid $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();

        // 调用 notify 方法来发送通知
        $order->user->notify(new OrderPaidNotification($order));
    }
}
