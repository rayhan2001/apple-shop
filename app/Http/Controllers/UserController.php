<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Helper\ResponseHelper;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function UserLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if ($validator->fails()) {
                return ResponseHelper::Out('error', $validator->errors(), 422);
            }

            $email = $request->input('email');
            $otp = rand(100000, 999999);
            $details = ['code' => $otp];
            Mail::to($email)->send(new OTPMail($details));
            if (Mail::failures()) {
                return ResponseHelper::Out('error', 'Failed to send OTP', 500);
            }

            User::updateOrCreate(['email' => $email],['email' => $email,'otp' => $otp]
            );

            return ResponseHelper::Out('success', 'A 6 digit OTP has been sent to your email', 200);
        }
        catch (\Exception $e) {
            return ResponseHelper::Out('error', $e->getMessage(), 500);
        }
    }

    public function VerifyLogin(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
            if ($user) {
                $user->otp = 0;
                $user->save();
                $token = JWTToken::createToken($user->email, $user->id);
                return ResponseHelper::Out('success', 'Login successful', 200)->cookie('token', $token, 60 * 24 * 30);
            } else {
                return ResponseHelper::Out('error', 'Invalid OTP', 401);
            }
        }
        catch (\Exception $e) {
            return ResponseHelper::Out('error', $e->getMessage(), 500);
        }
    }

    public function UserLogout(Request $request)
    {
        return redirect('/userLoginPage')->cookie('token', '', -1);
    }
}
