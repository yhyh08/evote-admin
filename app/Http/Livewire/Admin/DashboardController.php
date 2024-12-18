<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Election;

class DashboardController extends Component
{
    public $elections;

    public function mount()
    {
        $this->elections = Election::all(['election_topic', 'description', 'start_date', 'end_date', 'status']);
    }

    public function render()
    {
        return view('livewire.admin.dashboard', ['elections' => $this->elections]);
    }
}
