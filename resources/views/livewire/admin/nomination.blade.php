<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Candidates</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Election</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Candidate Name</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Position</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Status</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nominations as $election_id => $electionNominations)
                                            @php
                                                $firstNomination = $electionNominations->first();
                                                $nomineeCount = $electionNominations->count();
                                                $candidate = $firstNomination->candidate;
                                            @endphp
                                            <tr>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $firstNomination->election->election_topic ?? 'N/A' }}
                                                    </p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $candidate->candidate_name ?? 'N/A' }}
                                                        @if($nomineeCount > 1)
                                                            <span class="badge bg-info ms-1">+{{ $nomineeCount - 1 }} more</span>
                                                        @endif
                                                    </p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $candidate->position ?? 'N/A' }}
                                                    </p>
                                                </td>
                                                <td class="ps-3">
                                                    <span class="badge bg-{{ $candidate->status === 'Approved' ? 'success' : 'warning' }}">
                                                        {{ $candidate->status ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="ps-3">
                                                    <a href="#" wire:click.prevent="viewNomination({{ $election_id }})" 
                                                    class="me-3" data-bs-toggle="tooltip" 
                                                    data-bs-original-title="View details">
                                                        <i class="fa-solid fa-file-lines text-secondary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- View Details Modal -->
    @if($viewingNomination)
    <div class="modal fade show modal-backdrop-custom" tabindex="-1" role="dialog" style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h5 class="modal-title">Details Information</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Progress Steps -->
                    <div class="progress-steps mb-4">
                        <div class="d-flex justify-content-between">
                            <div class="step {{ $currentStep >= 1 ? 'active' : '' }}">
                                <span>01</span>
                                <p>Nominee</p>
                            </div>
                            <div class="step {{ $currentStep >= 2 ? 'active' : '' }}">
                                <span>02</span>
                                <p>Candidate Info</p>
                            </div>
                            <div class="step {{ $currentStep >= 3 ? 'active' : '' }}">
                                <span>03</span>
                                <p>Biography & Manifesto</p>
                            </div>
                            <div class="step {{ $currentStep >= 4 ? 'active' : '' }}">
                                <span>04</span>
                                <p>Documents</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step Content -->
                    @if($currentStep == 1)
                        <div class="step-content">
                            @forelse($selectedNomination as $candidateId => $nominations)
                                <div class="nominee-section mb-4">
                                    <h5 class="mb-3">Nominations for {{ $nominations->first()->candidate->candidate_name ?? 'N/A' }}</h5>
                                    @foreach($nominations as $nomination)
                                        <div class="nominee-details mb-3">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <p class="text-sm mb-0"><strong>Nominee Name:</strong></p>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="text-sm mb-0">{{ $nomination->nominee_name }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <p class="text-sm mb-0"><strong>Phone:</strong></p>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="text-sm mb-0">{{ $nomination->nominee_phone }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <p class="text-sm mb-0"><strong>Email:</strong></p>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="text-sm mb-0">{{ $nomination->nominee_email }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <p class="text-sm mb-0"><strong>Reason:</strong></p>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="text-sm mb-0">{{ $nomination->reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if(!$loop->last)
                                            <hr class="my-3">
                                        @endif
                                    @endforeach
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-4">
                                @endif
                            @empty
                                <div class="alert alert-info">
                                    No nominations found for this election.
                                </div>
                            @endforelse
                        </div>
                    @elseif($currentStep == 2)
                        <div class="step-content">
                            @foreach($selectedNomination as $candidateId => $nominations)
                                @php
                                    $candidate = $nominations->first()->candidate ?? null;
                                @endphp
                                @if($candidate)
                                    <div class="candidate-section mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center pb-0">
                                                <h5>{{ $candidate->position ?? 'Candidate Details' }}</h5>
                                                <span class="badge bg-{{ $candidate->status === 'Approved' ? 'success' : 'warning' }}">
                                                {{ $candidate->status }}
                                            </span>
                                            </div>
                                            <div class="card-body py-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Name</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_name ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Email</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_email ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Phone</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_phone ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">IC Number</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_ic ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Date of Birth</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_dob ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Position</label>
                                                            <p class="text-sm mb-2">{{ $candidate->position ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Gender</label>
                                                            <p class="text-sm mb-2">{{ $candidate->candidate_gender ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Nationality</label>
                                                            <p class="text-sm mb-2">{{ $candidate->nationality ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Religion</label>
                                                            <p class="text-sm mb-2">{{ $candidate->religion ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Department</label>
                                                            <p class="text-sm mb-2">{{ $candidate->job ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Income</label>
                                                            <p class="text-sm mb-2">RM {{ number_format($candidate->income ?? 0, 2) }}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-xs text-secondary mb-0">Marriage Status</label>
                                                            <p class="text-sm mb-2">{{ $candidate->marriage_status ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        No candidate information available.
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @elseif($currentStep == 3)
                        <div class="step-content">
                            @foreach($selectedNomination as $candidateId => $nominations)
                                @php
                                    $candidate = $nominations->first()->candidate ?? null;
                                @endphp
                                @if($candidate)
                                    <div class="candidate-section mb-4">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                <h5 class="mb-2">{{ $candidate->candidate_name }}</h5>
                                                    <h6 class="text-sm font-weight-bolder">Short Biography</h6>
                                                    <p class="text-sm mb-3">{{ $candidate->short_biography ?? 'No biography available.' }}</p>
                                                </div>
                                                <div>
                                                    <h6 class="text-sm font-weight-bolder">Manifesto</h6>
                                                    <p class="text-sm mb-0">{{ $candidate->manifesto ?? 'No manifesto available.' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        No candidate information available.
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @elseif($currentStep == 4)
                        <div class="step-content">
                            @foreach($selectedNomination as $candidateId => $nominations)
                                @php
                                    $candidate = $nominations->first()->candidate ?? null;
                                @endphp
                                @if($candidate)
                                    <div class="candidate-section mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">{{ $candidate->candidate_name }}'s Documents</h5>
                                            <span class="badge bg-{{ $candidate->status === 'Approved' ? 'success' : 'warning' }}">
                                                {{ $candidate->status }}
                                            </span>
                                        </div>
                                        <div class="card">
                                            <div class="card-body px-0 pt-0 pb-2">
                                                <div class="table-responsive p-0">
                                                    <table class="table align-items-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">ID</th>
                                                                <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Document</th>
                                                                <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Description</th>
                                                                <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($candidate->documents as $document)
                                                            <tr>
                                                                <td class="ps-3">
                                                                    <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                                                </td>
                                                                <td class="ps-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fa-regular fa-file me-2"></i>
                                                                        <p class="text-xs font-weight-bold mb-0">{{ $document->name }}</p>
                                                                    </div>
                                                                </td>
                                                                <td class="ps-3">
                                                                    <p class="text-xs font-weight-bold mb-0">{{ $document->description }}</p>
                                                                </td>
                                                                
                                                                <td class="ps-3">
                                                                    <a href="#" wire:click.prevent="viewDocument({{ $document->id }})" 
                                                                       class="me-3" data-bs-toggle="tooltip" 
                                                                       data-bs-original-title="View document">
                                                                        <i class="fa-solid fa-file-lines text-secondary"></i>
                                                                    </a>
                                                                    <a href="#" wire:click.prevent="downloadDocument({{ $document->id }})" 
                                                                       class="me-3" data-bs-toggle="tooltip" 
                                                                       data-bs-original-title="Download document">
                                                                        <i class="fa-solid fa-download text-secondary"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Approve/reject buttons -->
                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-danger me-2" wire:click.prevent="closeViewAndShowReject({{ $candidateId }})">
                                                Reject
                                            </button>
                                            <button type="button" class="btn btn-success" wire:click.prevent="approveCandidate({{ $candidateId }})">
                                                Approve
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        No documents available.
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($currentStep == 1)
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                    @elseif($currentStep == 2)
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                        <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                    @elseif($currentStep == 3)
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                        <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                    @else
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show modal-backdrop-custom"></div>
    @endif

    <!-- Reject Reason Modal -->
    @if($showRejectModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; z-index: 999999;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Nomination</h5>
                    <button type="button" class="btn-close" wire:click="closeRejectModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea 
                            wire:model.defer="rejectReason" 
                            class="form-control" 
                            rows="3" 
                            placeholder="Please enter the reason for rejection..."
                        ></textarea>
                        @error('rejectReason') 
                            <span class="text-danger">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeRejectModal">CANCEL</button>
                    <button type="button" class="btn btn-danger" wire:click="rejectCandidate">CONFIRM REJECT</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 999998;"></div>
    @endif

    <x-alert type="success" />
    <x-alert type="error" />
</div>

@push('scripts')
<script>
    window.addEventListener('hideModal', event => {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
</script>
@endpush