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

        $grid->column('id', 'Id');
        $grid->column('title', '商品名称');
        $grid->column('on_sale', '是否上架')->display(function ($on_sale) {
            return $on_sale ? '已上架' : '已下架';
        });
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
        $show->field('description', 'Description');
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

        $form->text('title', 'Title');
        $form->textarea('description', 'Description');
        $form->image('image', 'Image');
        $form->switch('on_sale', 'On sale')->default(1);
        $form->decimal('rating', 'Rating')->default(5.00);
        $form->number('sold_count', 'Sold count');
        $form->number('review_count', 'Review count');
        $form->decimal('price', 'Price');

        return $form;
    }
}
