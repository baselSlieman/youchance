<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chats = Chat::query()->orderBy('created_at')->paginate(5);
        return view("admin.chat.index",compact('chats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function createMessage(Chat $chat)
    {
        return view("admin.chat.createMessage",compact('chat'));
    }
    public function sendMessage(Request $request,Chat $chat)
    {
        $response = Telegram::sendMessage([
            'chat_id' => $chat->id,
            'text' => $request->message,
        ]);
        return redirect()->route('chats.index')->with('success','Message sent');
    }
    /**
     * Update the specified resource in storage.
     */
    public function edit(Chat $chat)
    {
        return view("admin.chat.edit",compact('chat'));
    }

    public function update(Request $request, Chat $chat)
    {
        if($request->balance>$chat->balance){
            $diff = $request->balance-$chat->balance;
            $response = Telegram::sendMessage([
                    'chat_id' => $chat->id,
                    'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمت إضافة '.$diff.' NSP إلى رصيدك' ,
            ]);
        }elseif($request->balance<$chat->balance){
            $diff = $chat->balance-$request->balance;
            $response = Telegram::sendMessage([
                'chat_id' => $chat->id,
                'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمّ خضم '.$diff.' NSP من رصيدك' ,
        ]);
        }
        $chat->fill($request->all())->save();
        return redirect()->route('chats.index')->with('success','Chat updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
