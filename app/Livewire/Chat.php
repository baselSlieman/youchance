<?php

namespace App\Livewire;

use App\Models\Chat as ModelsChat;
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
        $chats = ModelsChat::query()
        ->when($this->search, function ($query) {
            return $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
        })
        ->orderBy("created_at","DESC")
        ->paginate(10);

        return view('livewire.chat',compact('chats'));
    }
}
