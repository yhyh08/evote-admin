<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;

class ResultController extends Component
{
    public function render()
    {
        $elections = Election::with('organization')->get();
        return view('livewire.admin.result', [
            'elections' => $elections
        ]);
    }

    public function show($electionId)
    {
        $election = Election::with(['organization', 'candidates'])->findOrFail($electionId);
        
        // Group candidates by position
        $election->grouped_candidates = $election->candidates
            ->groupBy('position')
            ->map(function ($candidates) {
                return $candidates->map(function ($candidate) use ($candidates) {
                    $totalVotes = $candidates->sum('votes_count');
                    $candidate->percentage = $totalVotes > 0 
                        ? ($candidate->votes_count / $totalVotes) * 100 
                        : 0;
                    return $candidate;
                });
            });

        return view('livewire.admin.result-show', [
            'election' => $election
        ]);
    }
}
