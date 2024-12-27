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
        $this->currentStep = 4;
        $this->dispatch('closeModal');
    }

    public function rejectCandidate()
    {
        $this->validate([
            'rejectReason' => 'required|min:10',
        ]);

        try {
            $candidate = Candidate::find($this->currentCandidateId);
            
            if (!$candidate) {
                session()->flash('error', 'Candidate not found: ' . $this->currentCandidateId);
                return;
            }

            try {
                $updateResult = $candidate->update([
                    'status' => 'Rejected',
                    'reason' => $this->rejectReason
                ]);

                if (!$updateResult) {
                    session()->flash('error', 'Failed to update candidate status');
                    return;
                }

                $this->showRejectModal = false;
                $this->rejectReason = '';
                $this->currentCandidateId = null;
                
                session()->flash('success', 'Candidate rejected successfully');

            } catch (\Exception $e) {
                session()->flash('error', 'Update error: ' . $e->getMessage());
                return;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function approveCandidate($candidateId)
    {
        try {
            $candidate = Candidate::find($candidateId);
            if ($candidate) {
                $updateResult = $candidate->update([
                    'status' => 'Approved'
                ]);

                if (!$updateResult) {
                    session()->flash('error', 'Failed to update candidate status');
                    return;
                }

                $this->viewingNomination = false;
                $this->currentStep = 1;
                $this->selectedNomination = null;
                $this->currentCandidateId = null;

                session()->flash('success', 'Candidate has been approved successfully.');
                
                return redirect()->route('nomination');

            } else {
                session()->flash('error', 'Candidate not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve candidate. Error: ' . $e->getMessage());
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

    public function getAllCandidates(){
        $candidates = Candidate::all();
        return response()->json($candidates);
    }

    public function getCandidateData($candidateId)
    {
        try {
            $candidate = Candidate::with('documents')->find($candidateId);

            if (!$candidate) {
                return response()->json(['error' => 'Candidate not found.'], 404);
            }

            return response()->json(['candidate' => $candidate], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve candidate data. Error: ' . $e->getMessage()], 500);
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
