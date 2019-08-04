<?php
/**
 * 关闭订单队列任务
 * 延时执行
 */

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        $this->delay($delay);
    }

    /*
     * 执行关闭订单操作
     */
    public function handle()
    {
        // 订单已支付，不能关闭，直接返回
        if ($this->order->paid_at) {
            return;
        }

        // 若订单未支付，则可以关闭
        DB::transaction(function () {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['closed' => true]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
