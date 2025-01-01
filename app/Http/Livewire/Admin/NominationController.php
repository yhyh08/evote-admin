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

    // public function saveNominee(Request $request)
    // {
    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'nominee_name' => 'required|string|max:255',
    //         'nominee_phone' => 'required|string|max:15',
    //         'nominee_email' => 'required|email|max:255',
    //         'election_id' => 'required|exists:elections,election_id',
    //         'reason' => 'nullable|string|max:500',
    //         'org_id' => 'required|exists:organizations,id',
    //     ]);

    //     try {
    //         // Create a new nomination
    //         $nomination = new Nomination();
    //         $nomination->nominee_name = $validatedData['nominee_name'];
    //         $nomination->nominee_phone = $validatedData['nominee_phone'];
    //         $nomination->nominee_email = $validatedData['nominee_email'];
    //         $nomination->election_id = $validatedData['election_id'];
    //         $nomination->reason = $validatedData['reason'];
    //         $nomination->org_id = $validatedData['org_id'];
    //         $nomination->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Nominee information saved successfully.',
    //             'nomination' => $nomination
    //         ], 201);
    //     } catch (\Exception $e) {
    //         \Log::error('Error saving nominee:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to save nominee: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // // Add to NominationController.php
    // public function saveCandidate(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required',
    //         'phone' => 'required',
    //         'email' => 'required|email',
    //         'candidate_id' => 'required|exists:candidates,candidate_id',
    //         'election_id' => 'required|exists:elections,election_id',
    //         'position' => 'required|string|max:255',
    //         'status' => 'required|string|max:255',
    //         'reason' => 'nullable|string|max:500',
    //         'org_id' => 'required|exists:organizations,id',
    //         // Add other validations
    //     ]);

    //     $candidate = new Candidate($validatedData);
    //     $candidate->save();

    //     return response()->json(['candidate_id' => $candidate->candidate_id]);
    // }

    // public function saveCandidateDocs(Request $request)
    // {
    //     $request->validate([
    //         'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
    //         'candidate_id' => 'required|exists:candidates,candidate_id'
    //     ]);

    //     foreach ($request->file('documents') as $document) {
    //         $path = $document->store('candidate-docs');
            
    //         $doc = new CandidateDocs([
    //             'candidate_id' => $request->candidate_id,
    //             'document_path' => $path,
    //             'document_type' => $document->getClientOriginalExtension()
    //         ]);
    //         $doc->save();
    //     }

    //     return response()->json(['message' => 'Documents uploaded successfully']);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'election_id' => 'required|exists:elections,id',
    //         'user_id' => 'required|exists:users,id',
    //         'position' => 'required|string',
    //         'status' => 'required|in:pending,approved,rejected'
    //     ]);

    //     $nomination = Nomination::create($validated);

    //     return response()->json([
    //         'message' => 'Nomination created successfully',
    //         'nomination' => $nomination
    //     ], 201);
    // }

    // public function updateAdditionalInfo(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'education' => 'required|string',
    //         'experience' => 'required|string',
    //         'achievements' => 'nullable|string',
    //         'vision_statement' => 'required|string'
    //     ]);

    //     $candidate = Candidate::findOrFail($id);
    //     $candidate->update($validated);

    //     return response()->json([
    //         'message' => 'Additional information updated successfully',
    //         'candidate' => $candidate
    //     ]);
    // }

    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'position' => 'required|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $candidate = Candidate::create($validated);

        return response()->json([
            'message' => 'Step 1 saved successfully',
            'candidate_id' => $candidate->id
        ], 201);
    }

    public function saveStep2(Request $request)
    {
        Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'candidate_name' => 'required|string',
            'candidate_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'candidate_phone' => 'required|string',
            'candidate_email' => 'required|email',
            'candidate_gender' => 'required|string',
            'candidate_ic' => 'required|string',
            'candidate_dob' => 'required|string',
            'candidate_address' => 'required|string',
            'nationality' => 'required|string',
            'religion' => 'required|string',
            'job' => 'required|string',
            'income' => 'required|string',
            'position' => 'required|string',
            'marriage_status' => 'required|string',
            'short_biography' => 'nullable|string',
            'manifesto' => 'nullable|string',
            'position' => 'required|string',
            'status' => 'required|string',
            'reason' => 'nullable|string',
            'receive_date' => 'nullable|date',
            'approve_date' => 'nullable|date',
            'sign' => 'nullable|string',
            'votes_count' => 'nullable|integer',
            'election_id' => 'required|exists:elections,id',
        ]);

        try {
            // Handle image upload if image is provided
            if ($request->hasFile('candidate_image')) {
                $image = $request->file('candidate_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/candidate-images', $imageName);
                $validated['candidate_image'] = 'candidate-images/' . $imageName;
            } else {
                // If no image provided, set to null or a default value
                $validated['candidate_image'] = null;
            }

            Log::info('Validated data:', $validated);

            // Create new candidate record with all validated data
            $candidate = Candidate::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Step 2 saved successfully',
                'candidate' => $candidate
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving candidate:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Step 2: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveStep3(Request $request)
    {
        // First, let's log the exact data we're receiving
        Log::info('Incoming request data:', $request->all());

        try {
            // Check if we're receiving the data as a JSON string
            $data = $request->json()->all();
            
            Log::info('Parsed JSON data:', $data);

            $validator = Validator::make($data, [
                'short_biography' => 'required|string|max:1000',
                'manifesto' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                // Return detailed validation errors
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'received_data' => $data  // Show what data we actually received
                ], 422);
            }

            $validated = $validator->validated();
            
            // Make sure the fields are being passed correctly
            Log::info('Validated data:', $validated);

            $candidate = Candidate::findOrFail($validated['candidate_id']);
            $candidate->update([
                'short_biography' => $validated['short_biography'],
                'manifesto' => $validated['manifesto']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Step 3 saved successfully',
                'candidate' => $candidate
            ]);

        } catch (\Exception $e) {
            Log::error('Error in saveStep3:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Step 3',
                'error' => $e->getMessage(),
                'received_data' => $request->all()
            ], 500);
        }
    }

    public function saveStep4(Request $request)
    {
        $validated = $request->validate([
            'nominator_name' => 'required|string',
            'nominator_phone' => 'required|string',
            'nominator_email' => 'required|email',
            'reason' => 'required|string',
            'id' => 'required|exists:users,id',
            'election_id' => 'required|exists:elections,election_id',
        ]);

        $nomination = Nomination::create($validated);

        return response()->json([
            'message' => 'Step 4 saved successfully',
            'nomination' => $nomination
        ], 201);
    }

    public function saveStep5(Request $request)
    {
        $validated = $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'document_types.*' => 'required|string'
        ]);

        $uploadedDocs = [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $document) {
                $path = $document->store('candidate-documents', 'public');
                
                $candidateDoc = CandidateDoc::create([
                    'candidate_id' => $validated['candidate_id'],
                    'document_type' => $request->document_types[$index],
                    'file_path' => $path
                ]);

                $uploadedDocs[] = $candidateDoc;
            }
        }

        return response()->json([
            'message' => 'Step 5 saved successfully',
            'documents' => $uploadedDocs
        ], 201);
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
                'reason' => 'required|string',
                'nominee_id' => 'nullable|string',
                'cand_doc_id' => 'nullable|string',
            ]);

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
                    $validated['candidate_image'] = null; // Set to null if image processing fails
                }
            } else {
                $validated['candidate_image'] = null;
            }

            // Set default values
            $candidateData = array_merge($validated, [
                'candidate_image' => 'default.jpg',
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
}
