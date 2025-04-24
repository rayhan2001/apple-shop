<?php

namespace App\Helper;

use App\Models\Invoice;
use App\Models\SSLCommerzAccount;
use Illuminate\Support\Facades\Http;

class SSLCommerz
{
    public static function initiatePayment($profile, $payable, $tran_id, $user_email)
    {
        try {
            $ssl = SSLCommerzAccount::first();
            $response = Http::asForm()->post($ssl->init_url, [
                "store_id" => $ssl->store_id,
                "store_passwd" => $ssl->store_passwd,
                "total_amount" => $payable,
                "currency" => $ssl->currency,
                "tran_id" => $tran_id,
                "success_url" => "$ssl->success_url?tran_id=$tran_id",
                "fail_url" => "$ssl->fail_url?tran_id=$tran_id",
                "cancel_url" => "$ssl->cancel_url?tran_id=$tran_id",
                "ipn_url" => $ssl->ipn_url,
                "cus_name" => $profile->cus_name,
                "cus_email" => $user_email,
                "cus_add1" => $profile->cus_add,
                "cus_add2" => $profile->cus_add,
                "cus_city" => $profile->cus_city,
                "cus_state" => $profile->cus_city,
                "cus_postcode" => "1230",
                "cus_country" => $profile->cus_country,
                "cus_phone" => $profile->cus_phone,
                "cus_fax" => $profile->cus_phone,
                "shipping_method" => "Yes",
                "ship_name" => $profile->ship_name,
                "ship_add1" => $profile->ship_add,
                "ship_add2" => $profile->ship_add,
                "ship_city" => $profile->ship_city,
                "ship_state" => $profile->ship_city,
                "ship_postcode" => "1230",
                "ship_country" => $profile->ship_country,
                "product_name" => "Test Product",
                "product_category" => "Electronic",
                "product_profile" => "general",
                "product_price" => $payable,
            ]);
            return $response->json('desc');

        } catch (\Exception $e) {
            return $ssl;
        }
    }

    public static function initiateSuccess($tran_id)
    {
        Invoice::where(['tran_id' => $tran_id, 'val_id' => '0'])->update(['payment_status' => 'Success']);
        return 1;
    }

    public static function initiateCancel($tran_id)
    {
        Invoice::where(['tran_id' => $tran_id, 'val_id' => '0'])->update(['payment_status' => 'Cancel']);
        return 1;
    }

    public static function initiateFail($tran_id)
    {
        Invoice::where(['tran_id' => $tran_id, 'val_id' => '0'])->update(['payment_status' => 'Fail']);
        return 1;
    }

    public static function initiateIpn($tran_id, $status, $val_id)
    {
        Invoice::where(['tran_id' => $tran_id, 'val_id' => '0'])->update(['payment_status' => $status, 'val_id' => $val_id]);
        return 1;
    }
}