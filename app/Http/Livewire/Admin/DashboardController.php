<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Election;
use App\Models\Nomination;
use App\Models\VotingEligibility;
use App\Models\Organization;

class DashboardController extends Component
{
    public $elections;
    public $electionCount;
    public $nominationCount;
    public $votersCount;
    public $organizationCount;

    public function mount()
    {
        $this->elections = Election::all(['election_topic', 'description', 'start_date', 'end_date', 'status']);
        $this->electionCount = Election::count();
        $this->nominationCount = Nomination::count();
        $this->votersCount = VotingEligibility::count();
        $this->organizationCount = Organization::where('is_active', true)->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard', ['elections' => $this->elections]);
    }
}
