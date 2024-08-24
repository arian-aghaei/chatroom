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
        $users = User::select('name', 'username')
            ->where('last_interaction', '>=', now()->subSeconds(15))
            ->orderBy('name')
            ->get();
        return $users;
    }

    public function chats()
    {
        $chats = Chat::select('id', 'context', 'user_id', 'created_at')
            ->when(\request()->input('last_update'), function ($q, $lastUpdate){
                $q->where('created_at', '>=', $lastUpdate)
                    ->orderBy('created_at', 'desc');
            },
            function ($q){
                $q->where('created_at', '>=', now()->subSeconds(90))
                    ->orderBy('created_at', 'desc');
            })->get();



        return [$chats, ['last_update'=>now()]];
    }

    public function sendChat(Request $request)
    {
        $request->validate([
            'context'=>'required',
            'user_id'=>'required|numeric'
        ]);

        $chat = Chat::create([
            'context'=>$request->input('context'),
            'user_id'=>$request->input('user_id')
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
}
