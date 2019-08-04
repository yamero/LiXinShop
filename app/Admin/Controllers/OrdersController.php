<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class OrdersController extends AdminController
{
    use ValidatesRequests;

    protected $title = '订单管理';

    /*
     * 订单列表
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->column('id', 'Id');
        $grid->column('no', '订单号');
        $grid->column('user.name', '下单人');
        $grid->column('total_amount', '订单金额')->sortable();
        $grid->column('paid_at', '付款时间')->display(function ($paid_at) {
            return $paid_at ? $paid_at : '未付款';
        })->sortable();
        $grid->column('ship_status', '物流状态')->display(function ($ship_status) {
            return Order::$shipStatusMap[$ship_status] ?? '未知';
        });
        $grid->column('refund_status', '退款状态')->display(function ($refund_status) {
            return Order::$refundStatusMap[$refund_status] ?? '未知';
        });
        $grid->column('payment_method', '付款方式');
        $grid->column('closed', '是否关闭')->display(function ($closed) {
            return $closed ? '已关闭' : '正常';
        });
        $grid->column('remark', '备注');
        $grid->column('created_at', __('下单时间'));

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        return $grid;
    }

    /*
     * 订单详情
     */
    public function show($id, Content $content)
    {
        return $content->header('订单详情')->view('admin.orders.show', ['order' => Order::findOrFail($id)]);
    }

    /*
     * 发货
     */
    public function ship(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }

        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }

        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data'   => $data
        ]);

        // 返回上一页
        return redirect()->back();
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->display('address->address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'))->default('pending');
        $form->text('refund_no', __('Refund no'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'))->default('pending');
        $form->text('ship_data', __('Ship data'));
        $form->text('extra', __('Extra'));

        return $form;
    }
}
