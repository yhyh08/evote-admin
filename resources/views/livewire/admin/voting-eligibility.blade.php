<div class="position-relative">
    <main class="main-content h-100">
        <div class="container-fluid py-4">
            @if ($errorMessage)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            {!! $errorMessage !!}
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Import Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Import Eligible Voters</h5>
                        </div>
                        <div class="card-body">
                            @if (session()->has('success'))
                                <x-alert type="success" :message="session('success')" />
                            @endif

                            <form wire:submit.prevent="import">
                                <div class="mb-3">
                                    <label for="org_id" class="form-label">Organization</label>
                                    <select class="form-control" wire:model="org_id" id="org_id">
                                        <option value="">Select Organization</option>
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->org_id }}">
                                                {{ $organization->org_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('org_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">Excel File</label>
                                    <input type="file" class="form-control" wire:model="file" id="file">
                                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voter List Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Voter List</h5>
                            <div class="mt-3">
                                <select class="form-control" wire:model="selectedOrg" wire:change="filterByOrganization">
                                    <option value="">All Organizations</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->org_id }}">
                                            {{ $organization->org_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Organization</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($eligibleVoters as $index => $voter)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $voter->organization->org_name }}</td>
                                            <td>{{ $voter->name }}</td>
                                            <td>{{ $voter->email }}</td>
                                            <td>{{ $voter->phone }}</td>
                                            <td>
                                                <span class="badge bg-{{ $voter->is_active ? 'success' : 'danger' }}">
                                                    {{ $voter->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No voters found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div> 