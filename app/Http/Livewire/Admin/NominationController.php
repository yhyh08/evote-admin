<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\CandidateDocs;
use Livewire\WithPagination;

class NominationController extends Component
{
    use WithPagination;

    public $viewingNomination = false;
    public $showRejectModal = false;
    public $rejectReason = '';
    public $currentCandidateId = null;
    public $currentStep = 1;
    public $selectedNomination;

    protected $listeners = ['refreshComponent' => '$refresh'];

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
    }

    public function closeViewAndShowReject($candidateId)
    {
        $this->currentCandidateId = $candidateId;
        $this->viewingNomination = false;
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectReason = '';
        $this->viewingNomination = true;
        $this->dispatch('refreshComponent');
    }

    public function rejectCandidate()
    {
        $this->validate([
            'rejectReason' => 'required|min:10',
        ]);

        try {
            $candidate = Candidate::find($this->currentCandidateId);
            if ($candidate) {
                $candidate->update([
                    'status' => 'Rejected',
                    'reason' => $this->rejectReason
                ]);

                CandidateDocs::where('candidate_id', $this->currentCandidateId)
                    ->update(['status' => 'Reject']);

                $this->showRejectModal = false;
                $this->rejectReason = '';
                $this->currentCandidateId = null;
                
                session()->flash('message', 'Candidate has been rejected successfully.');
                session()->flash('alert-type', 'success');

                $this->dispatchBrowserEvent('close-modal');
                $this->dispatch('refreshComponent');
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Failed to reject candidate. Please try again.');
            session()->flash('alert-type', 'error');
        }
    }

    public function approveCandidate($candidateId)
    {
        try {
            $candidate = Candidate::find($candidateId);
            if ($candidate) {
                $candidate->update(['status' => 'Approved']);
                CandidateDocs::where('candidate_id', $candidateId)
                    ->update(['status' => 'Approve']);
                
                session()->flash('message', 'Candidate has been approved successfully.');
                session()->flash('alert-type', 'success');

                $this->showRejectModal = false;
                $this->rejectReason = '';
                $this->currentCandidateId = null;
                
                $this->dispatchBrowserEvent('close-modal');
                $this->dispatch('refreshComponent');
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Failed to approve candidate. Please try again.');
            session()->flash('alert-type', 'error');
        }
    }

    public function downloadDocument($documentId)
    {
        $document = CandidateDocs::find($documentId);
        if ($document) {
            // Add your download logic here
        }
    }

    public function viewDocument($documentId)
    {
        $document = CandidateDocs::find($documentId);
        if ($document) {
            // Add your view logic here
        }
    }

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
}
