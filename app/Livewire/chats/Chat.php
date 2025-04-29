<?php

namespace App\Livewire\chats;

use App\Models\Chat as ModelsChat;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Chat extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $chats = ModelsChat::query()
        ->when($this->search, function ($query) {
            return $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
        })
        ->orderBy("created_at","DESC")
        ->paginate(10);

        return view('livewire.chats.chat',compact('chats'));
    }
}
