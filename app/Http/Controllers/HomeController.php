<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user,Chat $chat)
    {
        $this->user = $user;
        $this->chat = $chat;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $id = Auth::id();
        $users = $this->user->where('id','!=',$id)->get();
        return view('home',compact('users'));
    }
    /**
     * Show the chat page.
     *
     * @return view
     */
    public function chat($id)
    {
        $fromId = Auth::user();
        $toId = $this->user->where('id',$id)->select('id','name')->first();
        $from = $fromId->id;
        $to = $toId->id;
        $chats = $this->chat->where(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $from)
                  ->where('to_id', '=', $to);
        })
        ->orWhere(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $to)
                  ->where('to_id', '=', $from);
        })->get();
        return view('chat',compact('fromId','toId','chats'));
    }
}