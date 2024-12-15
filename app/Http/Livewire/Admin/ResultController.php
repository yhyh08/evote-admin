<?php

namespace App\Http\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;

class ResultController extends Component
{
    public function render()
    {
        $elections = Election::with('organization')->get();
        
        // Debugging line
        // dd($elections);
        
        return view('livewire.admin.result', [
            'elections' => $elections
        ])->layout('layouts.app');
    }

    public function show($electionId)
    {
        $election = Election::with('organization')->findOrFail($electionId);
        
        return view('livewire.admin.result-show', [
            'election' => $election
        ])->layout('layouts.app');
    }
}
