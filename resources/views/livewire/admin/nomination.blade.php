<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Nominations & Candidates</h5>
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
                                            @foreach($electionNominations as $index => $nomination)
                                                @php
                                                    $candidate = collect($candidates)->firstWhere('candidate_id', $nomination->candidate_id);
                                                @endphp
                                                <tr>
                                                    <td class="ps-3">
                                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->parent->iteration }}</p>
                                                    </td>
                                                    <td class="ps-3">
                                                        <p class="text-xs font-weight-bold mb-0">{{ $nomination->election->name ?? 'Unknown Election' }}</p>
                                                    </td>
                                                    <td class="ps-3">
                                                        <p class="text-xs font-weight-bold mb-0">{{ $candidate->candidate_name ?? 'N/A' }}</p>
                                                    </td>
                                                    <td class="ps-3">
                                                        <p class="text-xs font-weight-bold mb-0">{{ $candidate->position ?? 'N/A' }}</p>
                                                    </td>
                                                    <td class="ps-3">
                                                        <span class="text-xs font-weight-bold mb-0">{{ $candidate->status ?? 'N/A' }}</span>
                                                    </td>
                                                    <td class="ps-3">
                                                        <a href="#" wire:click.prevent="viewNomination({{ $nomination->election_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="View details">
                                                            <i class="fa-solid fa-eye text-secondary"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
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
                    <h5 class="modal-title">Election Details</h5>
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
                        </div>
                    </div>

                    <!-- Step Content -->
                    @if($currentStep == 1)
                        <!-- Nominees Information -->
                        <div class="step-content">
                            @foreach($selectedNomination as $index => $nominee)
                                <div class="nominee-section mb-4">
                                    <h4 class="mb-3">Nominee {{ $index + 1 }}</h4>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <p class="text-sm mb-0"><strong>Name:</strong></p>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-sm mb-0">{{ $nominee->nominee_name }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <p class="text-sm mb-0"><strong>Phone:</strong></p>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-sm mb-0">{{ $nominee->nominee_phone }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <p class="text-sm mb-0"><strong>Email:</strong></p>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-sm mb-0">{{ $nominee->nominee_email }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <p class="text-sm mb-0"><strong>Reason:</strong></p>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-sm mb-0">{{ $nominee->reason }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-4">
                                @endif
                            @endforeach
                        </div>
                    @elseif($currentStep == 2)
                        <!-- Candidates Information -->
                        <div class="step-content">
                            @foreach($selectedNomination as $index => $nominee)
                                @php
                                    $candidate = collect($candidates)->firstWhere('candidate_id', $nominee->candidate_id);
                                @endphp
                                @if($candidate)
                                    <div class="candidate-section mb-4">
                                        <h4 class="mb-3">Candidate {{ $index + 1 }}</h4>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <p class="text-sm mb-0"><strong>Name:</strong></p>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="text-sm mb-0">{{ $candidate->candidate_name }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <p class="text-sm mb-0"><strong>Position:</strong></p>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="text-sm mb-0">{{ $candidate->position }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <p class="text-sm mb-0"><strong>Status:</strong></p>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="text-sm mb-0">{{ $candidate->status }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <p class="text-sm mb-0"><strong>Department:</strong></p>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="text-sm mb-0">{{ $candidate->job }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($currentStep == 1)
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                    @else
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                    @endif
                    
                    @if($currentStep < 2)
                        <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                    @else
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show modal-backdrop-custom"></div>
    @endif
</div> 
</div> 