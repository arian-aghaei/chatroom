<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function onlines()
    {
        $users = User::select('name', 'username', 'id')
            ->where('last_interaction', '>=', now()->subSeconds(60))
            ->orderBy('name')
            ->get();
        return $users;
    }

    public function chats()
    {
        $chats = Chat::with('user:id,name')
            ->select('id', 'text', 'userId', 'created_at')
            ->when(\request()->input('last_update'), function ($q, $lastUpdate){
                $q->where('created_at', '>=', $lastUpdate)
                    ->orderBy('created_at');
            },
            function ($q){
                $q //->where('created_at', '>=', now()->subSeconds(90))
                    ->orderBy('created_at');
            })->get();



        return ['chats'=>$chats, 'last_update'=>now()];
    }

    public function sendChat(Request $request)
    {
        $request->validate([
            'text'=>'required',
        ]);

        $userid = \auth()->user()['id'];

        $chat = Chat::create([
            'text'=>$request->input('text'),
            'userId'=>$userid
        ]);

        return response()->json([
            'status'=>'success',
            'message'=>'chat submitted',
            'chat'=>$chat
        ]);
    }

    public function updateUser(Request $request)
    {
        $userid = Auth::id();
        $user = User::findOrFail($userid);

        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'username'=>'required|unique:users',
            'password'=>'required'
        ]);

        $user->fill([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'username'=>$request->input('username'),
            'password'=>$request->input('password'),
        ]);

        $user->save();

        return response()->json([
            'status' => 'success',
            'message'=>'User Updated Successfully',
            'user'=>$user,
            'authorisation'=> [
//                'token' => $token,
                'type' => 'bearer'
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
    }
}
