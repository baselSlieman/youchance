<?php

namespace App\Livewire;

use Livewire\Component;

use function PHPSTORM_META\type;

class Night extends Component
{
    public $mode=false;
    public $icon='sun';
    public $type='light';
    public function render()
    {
        if(session('mode')==null){
            session(['mode' => 'light']);
        }
        return view('livewire.night');
    }
    public function UpdatedMode(){
        $this->mode != $this->mode;
        if(session('mode')==='light'){
            session(['mode' => 'dark']);
        }else{
            session(['mode' => 'light']);
        }
        $this->dispatch('change-theme');
    }
}
