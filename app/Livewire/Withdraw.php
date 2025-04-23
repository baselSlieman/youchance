<?php
namespace App\Livewire;
use App\Models\Withdraw as ModelsWithdraw;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Withdraw extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public $chat_id=null;
    public function render()
    {
        $withdraws = ModelsWithdraw::query()
        ->when($this->search, function ($query) {
            return $query->whereHas('chat', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
            });
        });
        if(isset($this->chat_id)){
            $withdraws->where("chat_id",$this->chat_id);
        }
        $withdraws = $withdraws->orderByRaw("status = 'requested' DESC, created_at DESC")
        ->with('chat')
        ->paginate(10);
        return view('livewire.withdraw',compact('withdraws'));
    }



}
