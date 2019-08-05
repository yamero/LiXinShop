<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '优惠券管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        $grid->column('id', 'Id');
        $grid->column('name', '名称');
        $grid->column('code', '优惠码');
        $grid->column('description', '描述');
        $grid->column('type', '类型')->display(function ($type) {
            return CouponCode::$typeMap[$type];
        });
        $grid->column('usage', '已使用')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
        $states = [
            'on'  => ['value' => true, 'text' => '使用中', 'color' => 'primary'],
            'off' => ['value' => false, 'text' => '已禁用', 'color' => 'default'],
        ];
        $grid->column('enabled', '是否在使用')->switch($states);
        //$grid->column('not_before', 'Not before');
        //$grid->column('not_after', 'Not after');
        $grid->column('created_at', '创建时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CouponCode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('code', __('Code'));
        $show->field('type', __('Type'));
        $show->field('value', __('Value'));
        $show->field('total', __('Total'));
        $show->field('used', __('Used'));
        $show->field('min_amount', __('Min amount'));
        $show->field('not_before', __('Not before'));
        $show->field('not_after', __('Not after'));
        $show->field('enabled', __('Enabled'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->text('name', __('Name'));
        $form->text('code', __('Code'));
        $form->text('type', __('Type'));
        $form->decimal('value', __('Value'));
        $form->number('total', __('Total'));
        $form->number('used', __('Used'));
        $form->decimal('min_amount', __('Min amount'));
        $form->datetime('not_before', __('Not before'))->default(date('Y-m-d H:i:s'));
        $form->datetime('not_after', __('Not after'))->default(date('Y-m-d H:i:s'));
        $form->switch('enabled', __('Enabled'));

        return $form;
    }
}
