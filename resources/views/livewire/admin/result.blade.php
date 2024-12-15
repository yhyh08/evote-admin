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
                                            <th class="text-left text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                                No
                                            </th>
                                            <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Election
                                            </th>
                                            <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Election Detail
                                            </th>
                                            <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Period
                                            </th>
                                            <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                                Status
                                            </th>
                                            <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
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
                                                    <a href="#" class="me-3" data-bs-toggle="tooltip" data-bs-original-title="Public Results">
                                                        <i class="fa-solid fa-earth-americas text-secondary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
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
</div> 