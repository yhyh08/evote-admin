<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;

class ResultShowController extends Component
{
    public $election;

    public function mount($election)
    {
        $this->election = Election::findOrFail($election);
    }

    public function render()
    {
        $this->election->load('candidates');
        
        // Calculate percentages for each candidate
        $totalVotes = $this->election->candidates->sum('votes_count');
        $this->election->candidates->each(function($candidate) use ($totalVotes) {
            $candidate->percentage = $totalVotes > 0 ? 
                ($candidate->votes_count / $totalVotes) * 100 : 0;
        }); 

        return view('livewire.admin.result-show')
            ->layout('layouts.app');
    }
} 