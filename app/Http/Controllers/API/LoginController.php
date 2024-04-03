<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|max:255',
            'password'=>'required|max:255',
            'device_token'=>'required|max:255',
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Login credentials are invalid',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        $user->update(['device_token' => $request->device_token]);
        
        return response()->json([
            'status' => true,
            'user'=>$user,
            'message' => 'Login Successful',
            'token'=>$token,
        ]);
    }

    public function logout(Request $request)
    {
        if($request->user()->currentAccessToken()->delete())
        {
            return response()->json([
                'status' => true,
                'message' => 'User has been logged out'
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }
}
