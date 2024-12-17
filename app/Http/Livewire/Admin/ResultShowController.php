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
            $query->orderBy('position');
        }])->findOrFail($election);

        $groupedCandidates = $this->election->candidates->groupBy('position')->map(function($candidates) {
            $totalVotesForPosition = $candidates->sum('votes_count');
            return $candidates->map(function($candidate) use ($totalVotesForPosition) {
                $candidate->percentage = $totalVotesForPosition > 0 
                    ? ($candidate->votes_count / $totalVotesForPosition) * 100 
                    : 0;
                return $candidate;
            });
        });

        $this->election->grouped_candidates = $groupedCandidates;

        // Debugging line
        // dd($this->election->grouped_candidates);
    }

    public function render()
    {
        $totalVotes = $this->election->candidates->sum('votes_count');
        $this->election->candidates->each(function($candidate) use ($totalVotes) {
            $candidate->percentage = $totalVotes > 0 ? 
                ($candidate->votes_count / $totalVotes) * 100 : 0;
        }); 

        return view('livewire.admin.result-show')
            ->layout('layouts.app');
    }
} 