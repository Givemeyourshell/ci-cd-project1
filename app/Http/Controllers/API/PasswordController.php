<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Validator;
use Hash;
use Mail;

class PasswordController extends Controller
{
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|max:255|exists:users,email',
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }
        
        $user=User::where('email', $request->email)->first();
        $user->update(['verification_code' => rand(100000, 999999)]);
        Mail::to($user->email)->send(new ForgotPasswordMail($user));
        return response()->json([
            'status' => true ,
            'id'=>$user->id,
            'message' => 'We have emailed you a password reset code.'
        ]);
    }
    
    public function verify_code(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:users,id',
            'forgot_password_code'=>'required|size:6'
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }
        
        $user=User::find($request->id);
        if($user->verification_code==$request->forgot_password_code)
        {
            $user->update(['verification_code' => null]);            

            return response()->json([
                'status' => true,
                'data'=>$user,
                'message' => 'Forgot password code verified successfully.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Forgot password code is invalid.'
        ]);
    }
    
    public function resend_code(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:users,id',
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }
        
        $user=User::find($request->id);        
        $user->update(['verification_code' => rand(000000, 999999)]);
        Mail::to($user->email)->send(new ForgotPasswordMail($user));
        return response()->json([
            'status' => true ,
            'id'=>$user->id,
            'message' => 'We have emailed you a fresh password reset code.'
        ]);
    }
    
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:users,id',
            'password'=>'required|min:8|max:255'
        ]);
        if($validator->fails()){
            $errors=array('error'=>$validator->errors());
            return response()->json($errors,400);
        }
        
        $user=User::find($request->id);
        $user->update(['password' => Hash::make($request->password)]);
        return response()->json([
            'status' => true,
            'data'=>$user,
            'message' => 'Your password has been reset successfully.'
        ]);
    }
}
