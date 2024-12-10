<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Nomination</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Name</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Phone</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Email</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Status</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nominations as $nomination)
                                        <tr>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $nomination->nominee_name }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $nomination->nominee_phone }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <span class="text-xs font-weight-bold mb-0">{{ $nomination->nominee_email }}</span>
                                            </td>
                                            <td class="ps-3">
                                                <span class="text-xs font-weight-bold mb-0">{{ $nomination->status }}</span>
                                            </td>
                                            <td class="ps-3">
                                            <a href="#" wire:click.prevent="viewNomination({{ $nomination->nominee_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="View nomination">
    <i class="fa-solid fa-eye text-secondary"></i>
</a>
                                                <a href="#" wire:click.prevent="confirmDelete({{ $nomination->nominee_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Delete nomination">
                                                    <i class="fas fa-trash text-secondary"></i>
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

    <!-- Delete Confirmation Modal -->
    @if($confirmingDeletion)
    <div class="modal fade show modal-backdrop-custom" tabindex="-1" role="dialog" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Nomination</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this nomination?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="deleteNomination">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show modal-backdrop-custom"></div>
    @endif

    <!-- View Nomination Modal -->
    @if($viewingNomination)
    <div class="modal fade show modal-backdrop-custom" tabindex="-1" role="dialog" style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h5 class="modal-title">Nomination Details</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-sm mb-2"><strong>Nominee Name:</strong></p>
                            <p class="text-sm text-dark">{{ $selectedNomination->nominee_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-sm mb-2"><strong>Status:</strong></p>
                            <p class="text-sm text-dark">{{ $selectedNomination->status }}</p>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-sm mb-2"><strong>Phone:</strong></p>
                            <p class="text-sm text-dark">{{ $selectedNomination->nominee_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-sm mb-2"><strong>Email:</strong></p>
                            <p class="text-sm text-dark">{{ $selectedNomination->nominee_email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="text-sm mb-2"><strong>Reason for Nomination:</strong></p>
                            <p class="text-sm text-dark">{{ $selectedNomination->reason }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show modal-backdrop-custom"></div>
    @endif

</div> 