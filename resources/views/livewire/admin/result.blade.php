<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Result</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                No
                                            </th>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Election
                                            </th>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Election Detail
                                            </th>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Period
                                            </th>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Status
                                            </th>
                                            <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($elections as $index => $election)
                                            <tr>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $index + 1 }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->election_topic ?? 'N/A' }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        @if($election->description)
                                                            {{ $election->description }}
                                                        @elseif($election->election)
                                                            {{ $election->election->description }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        @if($election->start_date && $election->end_date)
                                                            {{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }} - 
                                                            {{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                </td>
                                                <td class="ps-3">
                                                    <span class="text-xs font-weight-bold mb-0">{{ $election->status ? 'Active' : 'Inactive' }}</span>
                                                </td>
                                                <td class="ps-3">
                                                    <a href="{{ route('result.show', $election->election_id) }}" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="View Results">
                                                        <i class="fa-solid fa-file-lines text-secondary"></i>
                                                    </a>
                                                    <a href="#" class="text-success me-3" data-bs-toggle="modal" data-bs-target="#publishModal" data-election-id="{{ $election->election_id }}">
                                                        <i class="fas fa-globe text-secondary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">No elections found</p>
                                                </td>
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

    <!-- Modal for Publish Confirmation -->
    <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishModalLabel">Confirm Publish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to publish the results? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPublish">Publish</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedElectionId;

    document.querySelectorAll('[data-bs-target="#publishModal"]').forEach(button => {
        button.addEventListener('click', function() {
            selectedElectionId = this.getAttribute('data-election-id');
        });
    });

    document.getElementById('confirmPublish').addEventListener('click', function() {
        // Logic to check if the election period has ended
        const endDate = new Date("{{ \Carbon\Carbon::parse($election->end_date)->toDateString() }}");
        const today = new Date();
        
        if (endDate < today) {
            // Logic to publish the results
            // You may want to call a Livewire method or make an AJAX request here
            alert(`Results for election ID ${selectedElectionId} published successfully!`);
            // Close the modal
            $('#publishModal').modal('hide');
        } else {
            alert('You cannot publish results until the election period has ended.');
        }
    });
</script> 