<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\CandidateDocs;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function vote($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
        
            $candidate->votes_count = $candidate->votes_count + 1;
            $candidate->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Vote counted successfully',
                'votes_count' => $candidate->votes_count,
                'data' => $candidate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to count vote: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCandidatesByStatus($status = 'Pending')
    {
        try {
            $statusMap = [
                '1' => 'Pending',
                '2' => 'Approved',
                '3' => 'Rejected'
            ];

            $searchStatus = isset($statusMap[$status]) ? $statusMap[$status] : $status;

            $candidates = DB::table('candidates')
                ->select(
                    'candidates.candidate_id',
                    'candidates.candidate_name',
                    'candidates.candidate_phone',
                    'candidates.candidate_email',
                    'candidates.status',
                    'candidates.position',
                    'candidates.created_at',
                    'candidates.election_id',
                    'elections.election_topic',
                    'elections.start_date',
                    'elections.end_date',
                    'elections.type',
                    'elections.status as election_status'
                )
                ->leftJoin('elections', 'candidates.election_id', '=', 'elections.election_id')
                ->where('candidates.status', $searchStatus)
                ->get()
                ->map(function ($candidate) {
                    return [
                        'id' => $candidate->candidate_id,
                        'name' => $candidate->candidate_name,
                        'phone' => $candidate->candidate_phone ?? null,
                        'email' => $candidate->candidate_email ?? null,
                        'position' => $candidate->position ?? null,
                        'status' => $candidate->status,
                        'created_at' => $candidate->created_at,
                        'election' => [
                            'id' => $candidate->election_id,
                            'topic' => $candidate->election_topic,
                            'type' => $candidate->type,
                            'status' => $candidate->election_status,
                            'period' => [
                                'start' => $candidate->start_date,
                                'end' => $candidate->end_date
                            ]
                        ]
                    ];
                });

            // Group candidates by election topic
            $groupedCandidates = $candidates->groupBy(function ($candidate) {
                return $candidate['election']['topic'] ?? 'Unassigned';
            })->map(function ($candidates, $electionTopic) {
                $firstCandidate = $candidates->first();
                return [
                    'election_topic' => $electionTopic,
                    'election_details' => [
                        'id' => $firstCandidate['election']['id'],
                        'type' => $firstCandidate['election']['type'],
                        'status' => $firstCandidate['election']['status'],
                        'period' => $firstCandidate['election']['period']
                    ],
                    'candidates' => $candidates->map(function ($candidate) {
                        // Remove nested election data to avoid redundancy
                        unset($candidate['election']);
                        return $candidate;
                    })
                ];
            })->values();

            return response()->json([
                'success' => true,
                'status_requested' => $status,
                'status_mapped_to' => $searchStatus,
                'total_candidates' => $candidates->count(),
                'elections' => $groupedCandidates,
                'debug_info' => [
                    'total_in_db' => DB::table('candidates')->count(),
                    'status_counts' => DB::table('candidates')
                        ->select('status', DB::raw('count(*) as count'))
                        ->groupBy('status')
                        ->get()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getCandidatesByStatus:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch candidates: ' . $e->getMessage(),
                'debug_info' => [
                    'requested_status' => $status,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
