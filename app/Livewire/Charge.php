<?php

namespace App\Livewire;

use App\Models\Charge as ModelsCharge;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Charge extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public function render()
    {
        $charges = ModelsCharge::query()
        ->when($this->search, function ($query) {
            return $query->whereHas('chat', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
            })->orWhere('processid',$this->search);
        })
        ->orderByRaw("status = 'complete' DESC, created_at DESC")
        ->with('chat')
        ->paginate(10);

        return view('livewire.charge',compact('charges'));
    }
}
