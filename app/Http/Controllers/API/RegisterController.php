<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\VerifyUserMail;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Hash;
use Mail;

class RegisterController extends Controller
{
    function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'password'=>'required|min:8|max:255',
            'device_token'=>'required|max:255',
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'device_token'=>$request->device_token,
        ]);

        if(!$user)
        {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
            ], 500);
        }

        $user->update(['verification_code' => rand(100000, 999999)]);
        Mail::to($user->email)->send(new VerifyUserMail($user));
        return response()->json([
            'status' => true,
            'data' => $user,
            'message' => 'Please verify your email address'
        ]);
    }

    public function verify_code(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:users,id|numeric',
            'verification_code'=>'required|size:6'
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }

        $user = User::find($request->id);
        if($user->verification_code==$request->verification_code)
        {
            $user->update(['verification_code' => null, 'status' => 1, 'email_verified_at'=>Carbon::now()]);
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
            
            return response()->json([
                'status' => true,
                'data'=>$user,
                'message' => 'Email verified successfully',
                'token'=>$token
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Email verification code is invalid.'
        ]);
    }

    public function resend_code(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:users,id|numeric',
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }

        $user = User::find($request->id);
        $user->update(['verification_code' => rand(000000, 999999)]);
        Mail::to($user->email)->send(new VerifyUserMail($user));
        return response()->json([
            'status' => true ,
            'message' => 'We have emailed you a fresh verification code.'
        ]);
    }
}
