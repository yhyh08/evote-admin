<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;

class ResultShow extends Component
{
    public $election;

    public function mount($election)
    {
        $this->election = Election::findOrFail($election);
    }

    public function render()
    {
        return view('livewire.admin.result-show')
            ->layout('layouts.app');
    }
} 