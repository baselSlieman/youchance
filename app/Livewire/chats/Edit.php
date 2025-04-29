<?php

namespace App\Livewire\chats;

use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Telegram\Bot\Laravel\Facades\Telegram;

class Edit extends Component
{
    #[Validate('required|numeric')]
    public $balance;

    public $info;

    public $chat;

    public function mount($chat)
    {
        $this->chat = $chat;
        $this->balance = $chat->balance;
        $this->info = $chat->info;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.chats.edit');
    }

    public function save()
    {
        $this->validate();
        if($this->balance>$this->chat->balance){
            $diff = $this->balance-$this->chat->balance;
            $response = Telegram::sendMessage([
                    'chat_id' => $this->chat->id,
                    'text' => '🎖 عزيزي '.$this->chat->username.':'.PHP_EOL.' تمت إضافة '.$diff.' NSP إلى رصيدك' ,
            ]);
        }elseif($this->balance<$this->chat->balance){
            $diff = $this->chat->balance-$this->balance;
            $response = Telegram::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => '🎖 عزيزي '.$this->chat->username.':'.PHP_EOL.' تمّ خضم '.$diff.' NSP من رصيدك' ,
        ]);
        }
        $this->chat->update(
            $this->all()
        );
        session()->flash('success', trans('Success update chat').': '.$this->chat->id);
        return $this->redirect(route('chats.index'), navigate: true);


    }

    public function delete()
    {
        $this->chat->delete();
        session()->flash('success', trans('Success delete chat').': '.$this->chat->id);
        return $this->redirect(route('chats.index'), navigate: true);
    }



}
