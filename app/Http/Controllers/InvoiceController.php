<?php

namespace App\Http\Controllers;
use App\Helper\ResponseHelper;
use App\Helper\SSLCommerz;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = $request->header('id');
            $userEmail = $request->header('email');
            $profile = CustomerProfile::where('user_id', $userId)->first();
            $cus_details = "Name:$profile->cus_name, Address:$profile->cus_add, City:$profile->cus_city, Phone:$profile->cus_phone";
            $ship_details = "Name:$profile->ship_name, Address:$profile->ship_add, City:$profile->ship_city, Phone:$profile->ship_phone";
            $tran_id = uniqid();

            $total = 0;
            $total = ProductCart::where('user_id', $userId)->sum('price');

            $vat = ($total * 3) / 100;
            $payable = $total + $vat;

            $invoice = Invoice::create([
                'total' => $total,
                'vat' => $vat,
                'payable' => $payable,
                'cus_details' => $cus_details,
                'ship_details' => $ship_details,
                'tran_id' => $tran_id,
                'user_id' => $userId
            ]);

            $cartList = ProductCart::where('user_id', $userId)->get();
            foreach ($cartList as $cart) {
                InvoiceProduct::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cart->product_id,
                    'user_id' => $userId,
                    'qty' => $cart->qty,
                    'sale_price' => $cart->price,
                ]);
            }

            $paymentMethod = SSLCommerz::initiatePayment($profile, $payable, $tran_id, $userEmail);

            DB::commit();

            return ResponseHelper::Out('success', array(
                'paymentMethod' => $paymentMethod,
                'payable' => $payable,
                'vat' => $vat,
                'total' => $total,
            ), 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::Out('error', $th->getMessage(), 500);
        }
    }

    public function invoiceList(Request $request)
    {
        $userId = $request->header('id');
        return Invoice::where('user_id', $userId)->get();
    }

    public function invoiceProductList(Request $request)
    {
        $userId = $request->header('id');
        $invoiceId = $request->invoice_id;
        return InvoiceProduct::where(['user_id', $userId, 'invoice_id' => $invoiceId])->with('product')->get();
    }

    public function paymentSuccess(Request $request)
    {
        return SSLCommerz::initiateSuccess($request->tran_id);
    }
    public function paymentFail(Request $request)
    {
        return SSLCommerz::initiateFail($request->tran_id);
    }
    public function paymentCancel(Request $request)
    {
        return SSLCommerz::initiateCancel($request->tran_id);
    }

    public function paymentIpn(Request $request)
    {
        return SSLCommerz::initiateIpn($request->input('tran_id'), $request->input('status'), $request->input('val_id'));
    }
}
