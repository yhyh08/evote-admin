<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;

class NominationController extends Component
{
    public $confirmingDeletion = false;
    public $nominationToDelete;
    public $viewingNomination = false;
    public $selectedNomination = null;
    public $currentStep = 1;

    public function render()
    {
        return view('livewire.admin.nomination', [
            'nominations' => Nomination::with(['election'])->get(),
        ]);
    }

    public function confirmDelete($id)
    {
        $this->nominationToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteNomination()
    {
        $nomination = Nomination::findOrFail($this->nominationToDelete);
        $nomination->delete();
        
        $this->confirmingDeletion = false;
        $this->nominationToDelete = null;
        
        session()->flash('message', 'Nomination deleted successfully.');
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->nominationToDelete = null;
    }
    
    public function viewNomination($election_id)
    {
        $this->selectedNomination = Nomination::with('election')
            ->where('election_id', $election_id)
            ->get();
        $this->viewingNomination = true;
        $this->currentStep = 1;
    }

    public function closeViewModal()
    {
        $this->viewingNomination = false;
        $this->selectedNomination = null;
    }

    public function viewCandidate($candidateId)
    {
        // Add logic to view candidate details
        // This could open a modal or redirect to a candidate detail page
    }

    public function nextStep()
    {
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }
}
