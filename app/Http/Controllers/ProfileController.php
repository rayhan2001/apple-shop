<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function createProfile(Request $request)
    {
        $userId = $request->header('id');
        $request->merge(['user_id' => $userId]);

        $data = CustomerProfile::updateOrCreate(
            ['user_id' => $userId],
            $request->input()
        );

        return ResponseHelper::Out('Profile created successfully', $data, 200); 
    }

    public function readProfile(Request $request)
    {
        $userId = $request->header('id');
        $data = CustomerProfile::where('user_id', $userId)->first();
        return ResponseHelper::Out('success', $data, 200);
    }
}
