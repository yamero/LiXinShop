<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '注册会员';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->column('id', 'Id');
        $grid->column('name', '用户名');
        $grid->column('email', '邮箱');
        $grid->column('email_verified_at', '邮箱认证')->display(function ($email_verified_at) {
            return $email_verified_at ? '已认证' : '未认证';
        });
        $grid->column('created_at', '注册时间');
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('name', 'Name');
        $show->field('email', '邮箱');
        $show->field('email_verified_at', 'Email verified at');
        $show->field('password', 'Password');
        $show->field('remember_token', 'Remember token');
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
        $form = new Form(new User);

        $form->text('name', 'Name');
        $form->email('email', 'Email');
        $form->datetime('email_verified_at', 'Email verified at')->default(date('Y-m-d H:i:s'));
        $form->password('password', 'Password');

        return $form;
    }
}
