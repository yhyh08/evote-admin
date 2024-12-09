<div>
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="alert alert-success alert-dismissible" 
             role="alert">
            <span class="text-sm">{{ session('message') }}</span>
            <button type="button" 
                    class="btn-close text-lg py-3 opacity-10" 
                    data-bs-dismiss="alert" 
                    aria-label="Close"
                    @click="show = false">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Total Organization</h5>
                                </div>
                                <a href="#" wire:click="create" class="btn bg-gradient-primary btn-sm mb-0" type="button">
                                    <i class="fa-solid fa-circle-plus me-2 btn-add"></i>Add
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div class="row">
                                @forelse($organizations as $organization)
                                    <div class="col-md-6 mb-3">
                                        <ul class="list-group">
                                            <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                                <div class="d-flex flex-column w-75">
                                                    <h6 class="mb-3 text-sm">{{ $organization->org_name }}</h6>
                                                    <span class="mb-2 text-xs">PIC: <span class="text-dark font-weight-bold ms-2">{{ $organization->pic_name }}</span></span>
                                                    <span class="mb-2 text-xs">Created on: <span class="text-dark ms-2 font-weight-bold">{{ $organization->created_at->format('d/m/Y') }}</span></span>
                                                </div>
                                                <div class="ms-auto text-end mt-2">
                                                    <button wire:click="view({{ $organization->org_id }})" class="btn bg-gradient-secondary btn-sm mb-2 btn-func">
                                                        <i class="fas fa-eye me-2"></i>View More
                                                    </button>
                                                    <button wire:click="confirmActivate({{ $organization->org_id }})" 
                                                            class="btn bg-gradient-secondary btn-sm mb-2 btn-func"
                                                            {{ $organization->is_active ? 'disabled' : '' }}>
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        {{ $organization->is_active ? 'Activated' : 'Activate' }}
                                                    </button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-center">No organizations found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade @if($isOpen) show @endif" tabindex="-1" role="dialog" 
        style="display: @if($isOpen) block @else none @endif">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $org_id ? 'Edit Organization' : 'Add Organization' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Organization Name</label>
                                <input type="text" wire:model="org_name" class="form-control">
                                @error('org_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select wire:model="org_cat" class="form-select @error('org_cat') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                                @error('org_cat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label>Description</label>
                                <textarea wire:model="org_desc" class="form-control" rows="3"></textarea>
                                @error('org_desc') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label>Address</label>
                                <textarea wire:model="org_address" class="form-control" rows="2"></textarea>
                                @error('org_address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label>Organization Image</label>
                                <input type="file" wire:model="org_img" class="form-control">
                                @error('org_img') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <hr>
                            <h6>Person in Charge Details</h6>
                            <div class="col-md-4 mb-3">
                                <label>Name</label>
                                <input type="text" wire:model="pic_name" class="form-control">
                                @error('pic_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Phone</label>
                                <div class="input-group">
                                    <input type="text" 
                                           wire:model.debounce.500ms="pic_phone" 
                                           class="form-control @error('pic_phone') is-invalid @enderror">
                                    @error('pic_phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Email</label>
                                <input type="email" wire:model="pic_email" class="form-control">
                                @error('pic_email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade @if($confirmingDeletion) show @endif" tabindex="-1" role="dialog" 
        style="display: @if($confirmingDeletion) block @else none @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Organization</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingDeletion', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this organization?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('confirmingDeletion', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="delete">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade @if($viewMode) show @endif" tabindex="-1" role="dialog" 
        style="display: @if($viewMode) block @else none @endif">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                @if($selectedOrg)
                <div class="modal-header">
                    <h5 class="modal-title">Organization</h5>
                    <button type="button" class="btn-close" wire:click="closeView"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">{{ $selectedOrg->org_name }}</h4>
                        <div>
                            <button wire:click="edit({{ $selectedOrg->org_id }})" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button wire:click="confirmDelete({{ $selectedOrg->org_id }})" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            @if($selectedOrg->org_img)
                                <img src="{{ asset('storage/' . $selectedOrg->org_img) }}" class="img-fluid rounded mb-3">
                            @else
                                <div class="bg-light p-4 text-center rounded mb-3">
                                    <i class="fas fa-building fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Organization Name:</label>
                                    <p class="mb-3">{{ $selectedOrg->org_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Category:</label>
                                    <p class="mb-3">{{ $selectedOrg->org_cat }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-muted mb-1">Description:</label>
                                    <p class="mb-3">{{ $selectedOrg->org_desc }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-muted mb-1">Address:</label>
                                    <p class="mb-3">{{ $selectedOrg->org_address }}</p>
                                </div>
                                
                                <div class="col-12">
                                    <h6 class="mb-3">Person in Charge Details</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Name:</label>
                                            <p class="mb-3">{{ $selectedOrg->pic_name }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Phone:</label>
                                            <p class="mb-3">{{ $selectedOrg->pic_phone }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Email:</label>
                                            <p class="mb-3">{{ $selectedOrg->pic_email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeView">Close</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Activation Confirmation Modal -->
    <div class="modal fade @if($confirmingActivation) show @endif" tabindex="-1" role="dialog" 
        style="display: @if($confirmingActivation) block @else none @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activate Organization</h5>
                    <button type="button" class="btn-close" wire:click="closeActivateModal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to activate this organization?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeActivateModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success" wire:click="activate">
                        Yes, Activate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Style for disabled button -->
    <style>
    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Style for activated button */
    .btn:disabled i {
        color: #28a745;
    }

    .modal img.img-fluid {
        max-height: 200px;
        width: 100%;
        object-fit: cover;
    }
    </style>
</div> 