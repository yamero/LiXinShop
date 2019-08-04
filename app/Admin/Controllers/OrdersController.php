<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->column('id', 'Id');
        $grid->column('no', '订单号');
        $grid->column('user.name', '下单人');
        //$grid->column('address', '收货地址');
        $grid->column('total_amount', '订单金额')->sortable();
        $grid->column('paid_at', '付款时间')->display(function ($paid_at) {
            return $paid_at ? $paid_at : '未付款';
        });
        $grid->column('ship_status', '物流状态')->display(function ($ship_status) {
            return Order::$shipStatusMap[$ship_status] ?? '未知';
        });
        $grid->column('refund_status', '退款状态')->display(function ($refund_status) {
            return Order::$refundStatusMap[$refund_status] ?? '未知';
        });
        $grid->column('payment_method', '付款方式');
        $grid->column('remark', '备注');
        //$grid->column('payment_no', '付款单号');

        //$grid->column('refund_no', __('退款单号'));
        //$grid->column('closed', __('订单是否关闭'));
        //$grid->column('reviewed', __('是否已评价'));
        //$grid->column('ship_data', __('Ship data'));
        //$grid->column('extra', __('Extra'));
        $grid->column('created_at', __('下单时间'));
        //$grid->column('updated_at', __('更新时间'));

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content->header('订单详情')->view('admin.orders.show', ['order' => Order::findOrFail($id)]);
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
