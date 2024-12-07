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
                                <h5 class="mb-0">Election Committee</h5>
                            </div>
                            <a href="#" wire:click="create" class="btn bg-gradient-primary btn-sm mb-0" type="button" >
                                    <i class="fa-solid fa-circle-plus me-2 btn-add"></i>Add</a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            No
                                        </th>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            Name
                                        </th>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            Phone
                                        </th>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            Email
                                        </th>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            Status
                                        </th>
                                        <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($committees as $index => $committee)
                                        <tr>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $index + 1 }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $committee->com_name }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $committee->com_phone }}</p>
                                            </td>
                                            <td class="ps-3">
                                                <span class="text-xs font-weight-bold mb-0">{{ $committee->com_email }}</span>
                                            </td>
                                            <td class="ps-3">
                                                <span class="text-xs font-weight-bold mb-0">{{ $committee->isApprove ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td class="ps-3">
                                                <a href="#" wire:click.prevent="edit({{ $committee->com_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Edit user">
                                                    <i class="fas fa-user-edit text-secondary"></i>
                                                </a>
                                                <a href="#" wire:click.prevent="confirmDelete({{ $committee->com_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Delete user">
                                                    <i class="fas fa-trash text-secondary"></i>
                                                </a>
                                                <a href="#" wire:click.prevent="view({{ $committee->com_id }})" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="View user">
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
    </main>

    <!-- Add & Edit Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: {{ $isOpen ? 'block' : 'none' }};">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($isView)
                            View Election Committee
                        @elseif($committee_id)
                            Edit Election Committee
                        @else
                            Add Election Committee
                        @endif
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" wire:model="com_name" class="form-control" @if($isView) readonly @endif>
                        @error('com_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" wire:model="com_phone" class="form-control" @if($isView) readonly @endif>
                        @error('com_phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" wire:model="com_email" class="form-control" @if($isView) readonly @endif>
                        @error('com_email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select wire:model="isApprove" class="form-control" @if($isView) disabled @endif>
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('isApprove') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">Close</button>
                    @if(!$isView)
                        <button type="button" class="btn bg-gradient-primary btn-sm" wire:click="{{ $committee_id ? 'update' : 'store' }}">Save</button>
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
                    <h5 class="modal-title">Delete Election Committee</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this election committee member?
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