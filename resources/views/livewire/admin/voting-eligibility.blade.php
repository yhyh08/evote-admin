<div class="position-relative">
    <main class="main-content h-100">
        <div class="container-fluid py-4">
            @if ($errorMessage)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-danger alert-fixed" role="alert">
                            {!! $errorMessage !!}
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Import Eligible Voters</h5>
                        </div>

                        @if (session()->has('success'))
                            <x-alert type="success" :message="session('success')" />
                        @endif

                        <div class="card-body">
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
                                    <input type="file" class="form-control" wire:model.defer="file" id="file">
                                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voter List -->
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
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Organization</th>
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Name</th>
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Email</th>
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Phone</th>
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