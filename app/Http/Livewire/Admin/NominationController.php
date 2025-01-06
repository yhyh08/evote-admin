<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Nomination;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\CandidateDocs;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NominationController extends Component
{
    use WithPagination;

    public $viewingNomination = false;
    public $showRejectModal = false;
    public $rejectReason = '';
    public $currentCandidateId = null;
    public $currentStep = 1;
    public $selectedNomination;
    public $previewDocument = null;
    public $showPreviewModal = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function viewNomination($candidateId)
    {
        $nominations = Nomination::with(['candidate.documents', 'candidate'])
            ->where('candidate_id', $candidateId)
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
        try {
            $document = CandidateDocs::findOrFail($documentId);
            
            if (!Storage::disk('public')->exists($document->document)) {
                session()->flash('error', 'Document file not found.');
                return;
            }

            // Get the file path and mime type
            $filePath = Storage::disk('public')->path($document->document);
            $mimeType = Storage::disk('public')->mimeType($document->document);

            // Return response for file viewing
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($document->document) . '"'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error viewing document: ' . $e->getMessage());
            return;
        }
    }

    public function viewDocument($documentId)
    {
        try {
            $document = CandidateDocs::findOrFail($documentId);
            
            if (!Storage::disk('public')->exists($document->document)) {
                session()->flash('error', 'Document file not found.');
                return;
            }

            // Return URL to be opened in new tab
            return redirect()->to(url(Storage::url($document->document)));

        } catch (\Exception $e) {
            session()->flash('error', 'Error viewing document: ' . $e->getMessage());
        }
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewDocument = null;
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
        $nominations = Nomination::with(['candidate', 'candidate'])
            ->orderBy('candidate_id')
            ->get()
            ->groupBy('candidate_id');

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
        try {
            $validated = $request->validate([
                'election_id' => 'required',
                'user_id' => 'required',
                'candidate_name' => 'required',
                'candidate_image' => 'nullable|string',
                'candidate_phone' => 'required|string',
                'candidate_email' => 'required|email',
                'candidate_gender' => 'required|string',
                'candidate_ic' => 'required|string',
                'candidate_address' => 'required|string',
                'nationality' => 'required|string',
                'religion' => 'required|string',
                'job' => 'required|string',
                'income' => 'required|string',
                'position' => 'required|string',
                'marriage_status' => 'required|string',
                'short_biography' => 'nullable|string',
                'manifesto' => 'nullable|string',
                'reason' => 'nullable|string',
                'sign' => 'nullable|string',
                'status' => 'required|string',
                'votes_count' => 'required|integer',
                'nominee_id' => 'nullable|array',
                'cand_doc_id' => 'nullable|array',
            ]);

            $data = array_merge($validated, [
                'nominee_id' => json_encode($validated['nominee_id']),
                'cand_doc_id' => json_encode($validated['cand_doc_id']),
            ]);

            $user = User::find($data['user_id']);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $candidate = Candidate::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Candidate information saved successfully',
                'data' => $candidate
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error saving candidate:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save candidate information',
                'debug_message' => $e->getMessage()
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
                'candidate_id' => 'required|exists:candidates,candidate_id',
                'org_id' => 'required|exists:organizations,org_id'
            ]);

            $lastNomination = Nomination::latest('nominee_id')->first();
            $nextNomineeId = $lastNomination ? $lastNomination->nominee_id + 1 : 1;

            $nomination = new Nomination();
            $nomination->nominee_id = $nextNomineeId;
            $nomination->nominee_name = $validated['nominee_name'];
            $nomination->nominee_phone = $validated['nominee_phone'];
            $nomination->nominee_email = $validated['nominee_email'];
            $nomination->reason = $validated['reason'];
            $nomination->candidate_id = $validated['candidate_id'];
            $nomination->org_id = $validated['org_id'];

            if (!$nomination->save()) {
                throw new \Exception('Failed to save nomination to database');
            }

            return response()->json([
                'success' => true,
                'nominee_id' => $nomination->nominee_id,
                'message' => 'Nomination saved successfully',
                'data' => $nomination
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error saving nomination:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save nomination',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveDocuments(Request $request)
    {
        try {
            $request->validate([
                'candidate_id' => 'required|exists:candidates,candidate_id',
                'document' => 'required|file',
            ]);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Create directory path with candidate ID
                $directoryPath = 'documents/candidate_' . $request->candidate_id;
                
                // Store file in candidate-specific directory
                $path = $file->storeAs($directoryPath, $fileName, 'public');

                // Save the file path to the database
                $doc = new CandidateDocs();
                $doc->candidate_id = $request->candidate_id;
                $doc->document = $path;
                $doc->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Document saved successfully',
                    'file_path' => asset('storage/' . $path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No document file received'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Document upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCandidateId(Request $request)
    {
        try {
            // Get the most recent candidate submission
            $candidate = Candidate::latest()
                ->first(['candidate_id', 'candidate_name', 'created_at']);

            if (!$candidate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No candidate submissions found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate_name' => $candidate->candidate_name,
                    'submitted_at' => $candidate->created_at
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching candidate ID:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch candidate information',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }
}
