<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;

class LoginController extends Controller
{


    /**
     * @param Request $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $valid = validator::validate($request->all(), [
            'email'=>'required|email',
            'password'=>'required'
        ]);
//        if ($valid->fails()) {
//            return response()->json($valid->errors(), 422);
//        }

        $cridentials = $request->only('email', 'password');


        if(!Auth::attempt($cridentials)){
            return response()->json([
                'status'=>'error',
                'message'=>'unauthorized'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status'=> 'success',
            'user'=> $user,
            'authorisation'=> [
                'token' => $token,
                'type' => 'bearer'
            ]
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $request->validate( [
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'username'=>'required|unique:users',
            'password'=>'required'
        ]);



        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'last_interaction' => now()
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message'=>'User Registered Successfully',
            'user'=>$user,
            'authorisation'=> [
                'token' => $token,
                'type' => 'bearer'
            ]
        ]);
    }

    public function userDetails()
    {
        return auth()->user();
    }
}
