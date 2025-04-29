<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Language extends Component
{
    public $selectedLanguage = 'en';

    public function arabic()
    {
        session(['locale' => 'ar']);
        $this->dispatch('languageChanged');
    }

    public function english()
    {
        session(['locale' => 'en']);
        $this->dispatch('languageChanged');
    }

    public function render()
    {
        return view('livewire.language');
    }
}
