<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultShowController extends Component
{
    public $election;

    public function mount($election)
    {
        $this->election = Election::with(['candidates' => function($query) {
            $query->orderBy('votes_count', 'desc');
        }])->findOrFail($election);

        \Log::info('Candidates Data:', $this->election->candidates->toArray());

        $totalVotes = $this->election->candidates->sum('votes_count');
        $this->election->candidates->each(function($candidate) use ($totalVotes) {
            $candidate->percentage = $totalVotes > 0 ? 
                ($candidate->votes_count / $totalVotes) * 100 : 0;
        });

        $this->election->grouped_candidates = $this->election->candidates->groupBy('position');
    }

    public function downloadPdf($electionId)
    {
        $election = Election::with(['candidates' => function($query) {
            $query->orderBy('votes_count', 'desc');
        }])->findOrFail($electionId);

        $election->grouped_candidates = $election->candidates->groupBy('position');

        $pdf = Pdf::loadView('livewire.admin.result-pdf', compact('election'));
        return $pdf->download('election_results.pdf');
    }

    public function show($election)
    {
        $election = Election::with(['candidates' => function($query) {
            $query->orderBy('votes_count', 'desc');
        }])->findOrFail($election);

        \Log::info('Election Data:', $election->toArray());

        return view('livewire.admin.result-show', compact('election'));
    }

    public function render()
    {
        return view('livewire.admin.result-show');
    }

    public function layout()
    {
        return 'layouts.app';
    }
} 