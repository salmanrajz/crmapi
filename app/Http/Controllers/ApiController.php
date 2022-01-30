<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    //
    public function requestToken(Request $request)
    {
        //    $validator = $request->validate([
        $validator = Validator::make($request->all(), [ // <---
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        if ($validator->fails()) {
            // here we return all the errors message
            // return response()->json(['errors' => $validator->errors()], 422);
            return [
                'ResponseCode' => '0',
                'ResponseMessage' => 'error',
                'ResponseData' => $validator->errors(),
            ];
        }

        // $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
        // if (!$user) {
        //     return [
        //         'ResponseCode' => '0',
        //         'ResponseMessage' => 'error',
        //         'ResponseData' => array(
        //             'error' => array(
        //                 'message' => 'User Not Verified Yet',
        //             )
        //         ),
        //     ];
        // }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return [
                'ResponseCode' => '0',
                'ResponseMessage' => 'error',
                'ResponseData' => array(
                    'error' => array(
                        'message' => 'User not Exist',
                    )
                ),
            ];
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
            // return "SOmething Wrong";
        }
        $sam =  $user->createToken($request->device_name)->plainTextToken;
        $test_categories = exam_category::where('status', 1)->get();
        $details = array(
            'user_details' => $user,
            'test_categories' => $test_categories,
            'token' => array(
                'token_name' => $sam,

            ),
        );
        return response()->json(
            [
                'ResponseCode' => '1',
                'ResponseMessage' => 'Thank you for Login',
                'ResponseData' => $details,
                // 'ResponseToken' => $sam,
            ],
            200
        );
    }
}
