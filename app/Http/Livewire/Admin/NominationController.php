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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

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

    public function getSavedProgress($user_id)
    {
        $progress = Candidate::with(['nomination', 'documents'])
            ->where('user_id', $user_id)
            ->latest()
            ->first();

        return response()->json([
            'progress' => $progress
        ]);
    }

    public function saveCandidateInfo(Request $request)
    {
        Log::info('Incoming candidate data:', $request->all());

        try {
            $validated = $request->validate([
                'candidate_name' => 'required|string',
                'candidate_image' => 'nullable|string',
                'candidate_phone' => 'required|string',
                'candidate_email' => 'required|email',
                'candidate_gender' => 'required|string',
                'candidate_ic' => 'required|string',
                'candidate_dob' => 'required|date',
                'candidate_address' => 'required|string',
                'nationality' => 'required|string',
                'religion' => 'required|string',
                'job' => 'required|string',
                'income' => 'required|string',
                'position' => 'required|string',
                'marriage_status' => 'required|string',
                'short_biography' => 'nullable|string',
                'manifesto' => 'nullable|string',
                'reason' => 'required|string',
                'sign' => 'nullable|string',
                'election_id' => 'required|exists:elections,election_id',
                'user_id' => 'required|exists:users,user_id',
                'reason' => 'nullable|string',
                'nominee_id' => 'nullable|string',
                'cand_doc_id' => 'nullable|string',
            ]);

            // Get the last candidate_id
            $lastCandidate = Candidate::orderBy('candidate_id', 'desc')->first();
            $nextCandidateId = $lastCandidate ? $lastCandidate->candidate_id + 1 : 1;

            if (!isset($validated['candidate_image'])) {
                $validated['candidate_image'] = 'default.jpg';
            }   

            // Handle base64 image if provided
            if (!empty($validated['candidate_image']) && strpos($validated['candidate_image'], 'base64') !== false) {
                try {
                    $image_parts = explode(",", $validated['candidate_image']);
                    $image_base64 = end($image_parts);
                    $image_type = 'png'; // Default to PNG if type cannot be determined
                    $imageName = 'candidate_' . time() . '.' . $image_type;
                    
                    Storage::disk('public')->put('candidate-images/' . $imageName, base64_decode($image_base64));
                    $validated['candidate_image'] = 'candidate-images/' . $imageName;
                } catch (\Exception $e) {
                    Log::error('Image processing error:', [
                        'error' => $e->getMessage(),
                        'image_data_length' => strlen($validated['candidate_image'])
                    ]);
                    $validated['candidate_image'] = null;
                }
            } else {
                $validated['candidate_image'] = null;
            }

            // Set default values and include the new candidate_id
            $candidateData = array_merge($validated, [
                'candidate_id' => $nextCandidateId,
                'candidate_image' => $validated['candidate_image'] ?? 'default.jpg',
                'status' => 'Pending',
                'votes_count' => 0,
            ]);

            Log::info('Final candidate data before save:', $candidateData);

            $candidate = Candidate::create($candidateData);

            return response()->json([
                'success' => true,
                'message' => 'Candidate information saved successfully',
                'data' => $candidate
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error saving candidate information:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save candidate information',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'request_data' => $request->all(),
                    'validation_errors' => $e instanceof ValidationException ? $e->errors() : null
                ]
            ], 500);
        }
    }

    public function saveNomination(Request $request)
    {
        try {
            $validated = $request->validate([
                'nominee_name' => 'required|string',
                'nominee_phone' => 'required|string',
                'nominee_email' => 'required|email',
                'reason' => 'required|string|max:500',
                'election_id' => 'required|exists:elections,election_id',
                'candidate_id' => 'required|exists:candidates,candidate_id',
                'org_id' => 'required|exists:organizations,id'
            ]);

            // Get the last nominee_id
            $lastNomination = Nomination::orderBy('nominee_id', 'desc')->first();
            $nextNomineeId = $lastNomination ? $lastNomination->nominee_id + 1 : 1;

            // Create new nomination with incremented nominee_id
            $nomination = new Nomination();
            $nomination->nominee_id = $nextNomineeId;
            $nomination->nominee_name = $validated['nominee_name'];
            $nomination->nominee_phone = $validated['nominee_phone'];
            $nomination->nominee_email = $validated['nominee_email'];
            $nomination->reason = $validated['reason'];
            $nomination->election_id = $validated['election_id'];
            $nomination->candidate_id = $validated['candidate_id'];
            $nomination->org_id = $validated['org_id'];
            $nomination->save();

            return response()->json([
                'success' => true,
                'nominee_id' => $nomination->nominee_id,
                'message' => 'Nomination saved successfully',
                'data' => $nomination
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save nomination',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveCandidateDoc(Request $request)
    {
        try {
            $validated = $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'description' => 'required|string',
                'candidate_id' => 'required|exists:candidates,candidate_id'
            ]);

            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('candidate-documents', $fileName, 'public');

                // Create new candidate document record
                $candidateDoc = CandidateDocs::create([
                    'document' => $filePath,
                    'description' => $validated['description'],
                    'candidate_id' => $validated['candidate_id']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'data' => $candidateDoc
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No document file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
