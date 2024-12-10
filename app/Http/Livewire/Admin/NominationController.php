<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;

class NominationController extends Component
{
    public $confirmingDeletion = false;
    public $nominationToDelete;
    public $viewingNomination = false;
    public $selectedNomination = null;

    public function render()
    {
        $nominations = Nomination::all();
        return view('livewire.admin.nomination', compact('nominations'));
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
    
    public function viewNomination($nomination_id)
    {
        $this->selectedNomination = Nomination::find($nomination_id);
        $this->viewingNomination = true;
    }

    public function closeViewModal()
    {
        $this->viewingNomination = false;
        $this->selectedNomination = null;
    }
}
