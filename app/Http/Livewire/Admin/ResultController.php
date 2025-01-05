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

    public function getResult($electionId)
    {
        $election = Election::with(['organization', 'candidates'])
            ->where('is_published', true)
            ->findOrFail($electionId);
        
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

        return response()->json([
            'election' => $election
        ]);
    }

    public function publishResult($electionId)
    {
        $election = Election::findOrFail($electionId);
        $election->update(['status' => true]);

        return response()->json([
            'message' => 'Election result published successfully',
            'election' => $election
        ]);
    }

    public function getAllResults()
    {
        $elections = Election::with(['organization', 'candidates'])
            ->where('status', true)
            ->get()
            ->map(function ($election) {
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
                return $election;
            });

        return response()->json([
            'elections' => $elections
        ]);
    }
}
