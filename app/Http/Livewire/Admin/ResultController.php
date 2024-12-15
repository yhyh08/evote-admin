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
        $election = Election::with('organization')->findOrFail($electionId);
        return view('livewire.admin.result-show', [
            'election' => $election
        ]);
    }

    public function layout()
    {
        return 'layouts.app';
    }
}
