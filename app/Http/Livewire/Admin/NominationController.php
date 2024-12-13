<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\CandidateDocs;

class NominationController extends Component
{
    public $viewingNomination = false;
    public $currentStep = 1;
    public $selectedNomination;
    public $documentStatus = [];
    public $showRejectModal = false;
    public $rejectReason = '';
    public $currentCandidateId = null;
    public $currentDocumentId = null;

    public function render()
    {
        $nominations = Nomination::with(['election', 'candidate'])
            ->orderBy('election_id')
            ->get()
            ->groupBy('election_id');

        return view('livewire.admin.nomination', [
            'nominations' => $nominations,
            'candidates' => Candidate::all()
        ]);
    }

    public function viewNomination($electionId)
    {
        $nominations = Nomination::with(['candidate.documents', 'election'])
            ->where('election_id', $electionId)
            ->get();

        $groupedNominations = $nominations->mapToGroups(function ($nomination) {
            return [$nomination->candidate_id => $nomination];
        });

        $this->selectedNomination = $groupedNominations;
        $this->viewingNomination = true;
        $this->currentStep = 1;

        // Initialize document statuses
        foreach ($nominations as $nomination) {
            if ($nomination->candidate && $nomination->candidate->documents) {
                foreach ($nomination->candidate->documents as $document) {
                    $this->documentStatus[$document->id] = $document->status;
                }
            }
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function closeViewModal()
    {
        $this->viewingNomination = false;
        $this->currentStep = 1;
        $this->selectedNomination = null;
        $this->documentStatus = [];
    }

    public function downloadDocument($documentId)
    {
        // Add download logic
    }

    public function viewDocument($documentId)
    {
        // Add view logic
    }

    public function showRejectModal($documentId = null)
    {
        $this->showRejectModal = true;
        $this->currentDocumentId = $documentId;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectReason = '';
        $this->currentDocumentId = null;
    }

    public function rejectDocument()
    {
        $this->validate([
            'rejectReason' => 'required|min:10',
        ]);

        $document = CandidateDocs::find($this->currentDocumentId);
        if ($document) {
            $document->update([
                'status' => 'Reject',
                'reject_reason' => $this->rejectReason
            ]);
            
            session()->flash('success', 'Document has been rejected.');
        }

        $this->closeRejectModal();
    }

    public function updateDocumentStatus($documentId, $status)
    {
        $document = CandidateDocs::find($documentId);
        if ($document) {
            $document->update(['status' => $status]);
            session()->flash('success', 'Document status updated successfully.');
        }
    }
}
