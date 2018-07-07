<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{
    public function login () {
        if(Auth::attempt(['email' => request('email'),'password'=> request('password')])){
            $user = Auth::user();
            $success['token']= $user->createToken('MyApp')->accessToken;
            return response()->json(['success'=> $success], 200);
        }else {
            return response()->json(['error'=>'Unauthorized'],401);
        }
    }

    public function register (Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],401);
        }
        $input = $request->all();
        $res = User::where('email',$request->email) -> first();
        if($res){
            return response()->json(['error' => 'this email is already exists, try using another one!'],401);
        }
        
        $input['password']= bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name ;

        return response()->json(['success' =>$success],200);
    }

    public function getDetails () {
        $user = Auth::user();
        return response()->json(['success'=>$user],200);
    }
}
