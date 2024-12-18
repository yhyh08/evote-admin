<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;

class ResultShowController extends Component
{
    public $election;

    public function mount($election)
    {
        $this->election = Election::with(['candidates' => function($query) {
            $query->orderBy('votes_count', 'desc');
        }])->findOrFail($election);

        // Debug: Log the candidates data
        \Log::info('Candidates Data:', $this->election->candidates->toArray());

        // Calculate percentages
        $totalVotes = $this->election->candidates->sum('votes_count');
        $this->election->candidates->each(function($candidate) use ($totalVotes) {
            $candidate->percentage = $totalVotes > 0 ? 
                ($candidate->votes_count / $totalVotes) * 100 : 0;
        });

        // Group candidates by position
        $this->election->grouped_candidates = $this->election->candidates->groupBy('position');
    }

    public function render()
    {
        return view('livewire.admin.result-show');
    }
} 