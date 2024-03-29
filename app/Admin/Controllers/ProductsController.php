<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->filter(function ($filter) {
            $filter->like('title', '商品名称')->placeholder('请输入搜索关键字');
        });

        $grid->column('id', 'Id');
        $grid->column('title', '商品名称');
        $states = [
            'on'  => ['value' => true, 'text' => '上架', 'color' => 'primary'],
            'off' => ['value' => false, 'text' => '下架', 'color' => 'default'],
        ];
        $grid->column('on_sale', '是否上架')->switch($states);
        $grid->column('rating', '评分');
        $grid->column('sold_count', '销量');
        $grid->column('review_count', '评论数');
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('title', 'Title');
        $show->field('description', '商品详情');
        $show->field('image', 'Image');
        $show->field('on_sale', 'On sale');
        $show->field('rating', 'Rating');
        $show->field('sold_count', 'Sold count');
        $show->field('review_count', 'Review count');
        $show->field('price', 'Price');
        $show->field('created_at', 'Created at');
        $show->field('updated_at', 'Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->text('title', '商品名称')->rules('required');
        $form->ckeditor('description', '商品详情')->rules('required');
        $form->image('image', '商品封面图')->rules('required');
        $form->multipleImage('images', '商品相册')->removable();
        $states = [
            'on'  => ['value' => true, 'text' => '上架', 'color' => 'primary'],
            'off' => ['value' => false, 'text' => '下架', 'color' => 'default'],
        ];
        $form->switch('on_sale', '是否上架')->states($states)->default(1);
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('sku_title', 'SKU 名称')->rules('required');
            $form->text('sku_description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });
        $form->decimal('rating', '评分')->default(5.00);
        $form->number('sold_count', '销量')->default(0);
        $form->number('review_count', '评论数')->default(0);

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: $form->model()->price;
        });


        return $form;
    }
}
