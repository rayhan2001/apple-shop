<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\ProductCart;
use App\Models\ProductDetail;
use App\Models\ProductReview;
use App\Models\ProductSlider;
use App\Models\ProductWishlist;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function ListProductByCategory(Request $request)
    {
        $data = Product::where('category_id', $request->id)->with('brand', 'category')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function ListProductByRemark(Request $request)
    {
        $data = Product::where('remark', $request->remark)->with('brand', 'category')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function ListProductByBrand(Request $request)
    {
        $data = Product::where('brand_id', $request->id)->with('brand', 'category')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function ListProductBySlider(Request $request)
    {
        $data = ProductSlider::get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function ProductDetailsById(Request $request)
    {
        $data = ProductDetail::where('product_id', $request->id)->with('product', 'product.brand', 'product.category')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function ListReviewByProduct(Request $request)
    {
        $data = ProductReview::where('product_id', $request->product_id)->with(['profile' => function ($query) {
            $query->select('id', 'cus_name');
        }])->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function productReview(Request $request)
    {
        $userId = $request->header('id');
        $profile = CustomerProfile::where('user_id', $userId)->first();
        if ($profile) {
            $request->merge(['customer_id' => $profile->id]);

            $data = ProductReview::updateOrCreate(
                [
                    'product_id' => $request->product_id,
                    'customer_id' => $profile->id
                ],
                $request->input()
            );
            return ResponseHelper::Out('success', $data, 200);
        } else {
            return ResponseHelper::Out('error', 'Customer profile not found', 404);
        }
    }

    public function productWishlist(Request $request)
    {
        $userId = $request->header('id');
        $data = ProductWishlist::where('user_id', $userId)->with('product')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function createWishlist(Request $request)
    {
        $userId = $request->header('id');
        $data = ProductWishlist::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $request->product_id,
            ],
            [
                'user_id' => $userId,
                'product_id' => $request->product_id,
            ]
        );
        return ResponseHelper::Out('success', $data, 200);
    }

    public function removeWishlist(Request $request)
    {
        $userId = $request->header('id');
        $data = ProductWishlist::where('user_id', $userId)->where('product_id', $request->product_id)->delete();
        if ($data) {
            return ResponseHelper::Out('success', 'Product removed from wishlist', 200);
        } else {
            return ResponseHelper::Out('error', 'Product not found in wishlist', 404);
        }
    }

    public function createCartList(Request $request)
    {
        $userId = $request->header('id');
        $productId = $request->product_id;
        $color = $request->color;
        $size = $request->size;
        $qty = $request->qty;
        $unitPrice = 0;

        $product = Product::where('id', $productId)->first();
        if ($product->discount == 1) {
            $unitPrice = $product->discount_price;
        }else{
            $unitPrice = $product->price;
        }
        $totalPrice = $unitPrice * $qty;
        $data = ProductCart::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId
            ],
            [
                'user_id' => $userId,
                'product_id' => $productId,
                'color' => $color,
                'size' => $size,
                'qty' => $qty,
                'price' => $totalPrice
            ]
        );
        return ResponseHelper::Out('success', $data, 200);
    }

    public function cartList(Request $request)
    {
        $userId = $request->header('id');
        $data = ProductCart::where('user_id', $userId)->with('product')->get();
        return ResponseHelper::Out('success', $data, 200);
    }

    public function removeCart(Request $request)
    {
        $userId = $request->header('id');
        $data = ProductCart::where('user_id', $userId)->where('product_id', $request->product_id)->delete();
        if ($data) {
            return ResponseHelper::Out('success', 'Product removed from cart', 200);
        } else {
            return ResponseHelper::Out('error', 'Product not found in cart', 404);
        }
    }
}
