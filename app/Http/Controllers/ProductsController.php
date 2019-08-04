<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /*
     * 商品列表页
     */
    public function index(Request $request)
    {
        $builder = Product::query()->where('on_sale', true);

        if ($searchKeyword = $request->input('search', '')) {
            $like = "%{$searchKeyword}%";
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('sku_title', 'like', $like)
                            ->orWhere('sku_description', 'like', $like);
                    });
            });
        }

        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        return view('products.index', [
            'products' =>  $products,
            'filters' => [
                'search_keyword' => $searchKeyword,
                'order' => $order
            ]
        ]);
    }

    /*
     * 商品详情页
     */
    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('抱歉，商品未上架，暂时无法购买');
        }

        // 当前商品是否被收藏
        $favorited = false;
        if ($user = $request->user()) {
            $favorited = boolval($user->favoriteProducts()->find($product->id));
        }

        // 商品评价
        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // 预先加载关联关系
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // 筛选出已评价的
            ->orderBy('reviewed_at', 'desc') // 按评价时间倒序
            ->limit(10) // 取出 10 条
            ->get();

        return view('products.show', [
            'product' => $product,
            'favorited' => $favorited,
            'reviews' => $reviews
        ]);
    }

    /*
     * 收藏商品
     */
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();

        if ($user->favoriteProducts()->find($product->id)) {
            return ['status' => 2, 'msg' => '您已经收藏过此商品了']; // 已收藏，直接返回
        }

        $user->favoriteProducts()->attach($product->id);

        return ['status' => 1, 'msg' => '收藏成功'];
    }

    /*
     * 取消收藏
     */
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product->id);

        return ['status' => 1, 'msg' => '已取消收藏'];
    }

    /*
     * 收藏商品列表
     */
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', [
            'products' => $products
        ]);
    }
}
