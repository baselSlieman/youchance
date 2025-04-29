<?php

namespace App\Livewire\chats;

use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Telegram\Bot\Laravel\Facades\Telegram;

use function PHPUnit\Framework\isEmpty;

class Message extends Component
{
    public $chat;
    public $message;
    public $done;
    public function mount($chat)
    {
        $this->chat = $chat;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.chats.message');
    }


    public function send()
    {
        $validated = $this->validate([
            'message' => 'required',
        ]);

            $response = Telegram::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => $this->message,
            ]);
            if ($response) {

                session()->flash('success', trans('Message sent to user').': '.$this->chat->id);

            }else{
                session()->flash('danger', trans('Failed send to user ').$this->chat->id);
                return $this->redirect(route('chats.index'), navigate: true);
            }

    }


}
