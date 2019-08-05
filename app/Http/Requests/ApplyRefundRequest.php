<?php
/**
 * 用户申请退款时对提交的数据进行验证
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyRefundRequest extends FormRequest
{
    public function rules()
    {
        return [
            'reason' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'reason' => '原因',
        ];
    }
}
