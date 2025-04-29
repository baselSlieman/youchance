<?php

namespace App\Livewire\charges;

use App\Models\Charge as ModelsCharge;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Charge extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public $chat_id=null;
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $charges = ModelsCharge::query()
        ->when($this->search, function ($query) {
            return $query->whereHas('chat', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
            })->orWhere('processid',$this->search);
        });
        if(isset($this->chat_id)){
            $charges->where("chat_id",$this->chat_id);
        }

        $charges = $charges->orderByRaw("status = 'pending' DESC, created_at DESC")
        ->with('chat')
        ->paginate(10);

        return view('livewire.charges.charge',compact('charges'));
    }

}
