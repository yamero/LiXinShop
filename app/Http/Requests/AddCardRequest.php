<?php
/**
 * 商品加入购物车时提交数据验证
 */

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;

class AddCardRequest extends FormRequest
{
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('商品不存在');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('商品已下架');
                    }
                    if ($sku->stock <= 0) {
                        return $fail('商品已售完');
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        return $fail('商品库存不足');
                    }
                }
            ],
            'amount' => ['required', 'integer', 'min:1']
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }
}
