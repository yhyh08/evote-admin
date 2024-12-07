<div>
    <x-alert type="success" />
    <x-alert type="error" />

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Election</h5>
                                </div>
                                <a href="#" wire:click="create" class="btn bg-gradient-primary btn-sm mb-0" type="button">
                                    <i class="fa-solid fa-circle-plus me-2 btn-add"></i>Add
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Topic</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Start Date</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">End Date</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Status</th>
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($elections as $index => $election)
                                            <tr>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $index + 1 }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->election_topic }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->start_date }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->end_date }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <span class="text-xs font-weight-bold mb-0">{{ $election->status }}</span>
                                                </td>
                                                <td class="ps-3">
                                                    <a href="#" wire:click.prevent="edit({{ $election->election_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Edit election">
                                                        <i class="fas fa-user-edit text-secondary"></i>
                                                    </a>
                                                    <a href="#" wire:click.prevent="confirmDelete({{ $election->election_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Delete election">
                                                        <i class="fas fa-trash text-secondary"></i>
                                                    </a>
                                                    <a href="#" wire:click.prevent="view({{ $election->election_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="View election">
                                                        <i class="fa-solid fa-file-lines text-secondary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No elections found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add & Edit Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: {{ $isOpen ? 'block' : 'none' }};">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($isView)
                            View Election
                        @elseif($election_id)
                            Edit Election
                        @else
                            Add Election
                        @endif
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Topic</label>
                        <input type="text" wire:model="election_topic" class="form-control" @if($isView) readonly @endif>
                        @error('election_topic') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select wire:model="type" class="form-control" @if($isView) disabled @endif>
                            <option value="">Select Type</option>
                            <option value="general">General</option>
                            <option value="special">Special</option>
                        </select>
                        @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Position</label>
                        <input type="text" wire:model="position" class="form-control" @if($isView) readonly @endif>
                        @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea wire:model="description" class="form-control" @if($isView) readonly @endif></textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Nomination Period</label>
                        <input type="date" 
                               wire:model="nominate_period" 
                               class="form-control" 
                               min="{{ date('Y-m-d') }}"
                               @if($isView) readonly @endif>
                        @error('nominate_period') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" 
                               wire:model="start_date" 
                               class="form-control" 
                               min="{{ date('Y-m-d') }}"
                               @if($isView) readonly @endif>
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>End Date</label>
                        <input type="date" 
                               wire:model="end_date" 
                               class="form-control" 
                               min="{{ date('Y-m-d') }}"
                               @if($isView) readonly @endif>
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select wire:model="status" class="form-control" @if($isView) disabled @endif>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">Close</button>
                    @if(!$isView)
                        <button type="button" class="btn bg-gradient-primary btn-sm" wire:click="{{ $election_id ? 'update' : 'store' }}">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($isOpen)
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: {{ $confirmingDeletion ? 'block' : 'none' }};">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Election</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this election?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('confirmingDeletion', false)">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @if($confirmingDeletion)
        <div class="modal-backdrop fade show"></div>
    @endif
</div> 