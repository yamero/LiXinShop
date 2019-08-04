<?php
/**
 * 订单支付成功，给用户发送通知
 * 在SendOrderPaidMail监听器中，调用这个通知
 */

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /*
     * 这里通过数据库和邮件两种形式发送通知，为便于测试，先只发送数据库形式的
     * 邮件通知会发送到uses表中的email字段指定的邮箱
     * 数据库通知会将toArray方法返回的数据转为json格式，并保存到notifications表中的data字段中
     */
    public function via($notifiable)
    {
        //return ['database', 'mail'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('订单支付成功')  // 邮件标题
            ->greeting($this->order->user->name.'您好：') // 欢迎词
            ->line('您于 '.$this->order->created_at->format('m-d H:i').' 创建的订单已经支付成功。') // 邮件内容
            ->action('查看订单', route('orders.show', [$this->order->id])) // 邮件中的按钮及对应链接
            ->success(); // 按钮的色调
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_no' => $this->order->no,
            'message' => '订单支付成功'
        ];
    }
}
