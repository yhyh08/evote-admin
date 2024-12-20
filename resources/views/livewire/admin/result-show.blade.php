<x-layouts.app>
<div class="main-content position-relative max-height-vh-100 h-100">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $election->election_topic }}</h6>
                                <p class="text-sm mb-0">
                                    Period: {{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('result') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-chevron-left me-2"></i>Back
                                </a>
                                <a href="{{ route('result.pdf', $election) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#publishModal">
                                    <i class="fas fa-globe me-2"></i>Publish
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="p-4">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Candidates Results</h6>
                            @forelse($election->grouped_candidates as $position => $candidates)
                                <h6 class="text-primary text-uppercase text-xs font-weight-bolder mt-4">{{ $position }}</h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Profile</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Candidate</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ballot</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ballot Percentages (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $candidate)
                                            <tr>
                                                <td class="align-middle text-sm">
                                                    <div class="avatar">
                                                        @if($candidate->candidate_image)
                                                            <img src="{{ asset('storage/candidate/' . basename($candidate->candidate_image)) }}" 
                                                                 alt="{{ $candidate->candidate_name }}" 
                                                                 class="rounded-circle"
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('storage/candidate/defaultProfile.jpg') }}"
                                                                 alt="Default Profile"
                                                                 class="rounded-circle"
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="align-middle text-sm">
                                                    <p class="text-sm mb-0">{{ $candidate->candidate_name }}</p>
                                                    <p class="text-xs text-muted mb-0">{{ $candidate->position }}</p>
                                                </td>
                                                <td class="align-middle text-sm">{{ $candidate->votes_count }}</td>
                                                <td class="align-middle text-sm">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                                style="width: {{ $candidate->percentage }}%" 
                                                                aria-valuenow="{{ $candidate->percentage }}" 
                                                                aria-valuemin="0" 
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span>{{ number_format($candidate->percentage, 1) }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    No candidates found for this election.
                                </div>
                            @endforelse

                            <div class="mt-5">
                                <h6 class="mb-3">Statistics</h6>
                                <canvas id="candidatesChart"></canvas>
                            </div>
                            <div class="mt-5">
                                <h6 class="mb-3">Candidates by Position</h6>
                                <div class="row">
                                    @foreach($election->grouped_candidates as $position => $candidates)
                                        <div class="col-md-4 mb-4">
                                            <h5 class="text-primary mt-4 mb-3">{{ $position }}</h5>
                                            <canvas id="pieChart-{{ $position }}" width="400" height="200"></canvas>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Publish -->
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('candidatesChart').getContext('2d');
        const candidatesData = @json($election->grouped_candidates);
        
        const labels = Object.keys(candidatesData).flatMap(position => 
            candidatesData[position].map(candidate => candidate.candidate_name)
        );

        const data = Object.keys(candidatesData).flatMap(position => 
            candidatesData[position].map(candidate => candidate.votes_count)
        );

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Votes Count',
                    data: data,
                    backgroundColor: 'rgb(175, 81, 225)',
                    borderColor: 'rgb(175, 81, 225)',
                    borderWidth: 1,
                    barThickness: 200,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        Object.keys(candidatesData).forEach(position => {
            const pieCtx = document.getElementById(`pieChart-${position}`).getContext('2d');
            const pieLabels = candidatesData[position].map(candidate => candidate.candidate_name);
            const pieData = candidatesData[position].map(candidate => candidate.votes_count);

            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        label: 'Votes Count',
                        data: pieData,
                        backgroundColor: pieLabels.map(() => 
                            `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`
                        ),
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: `Votes Distribution for ${position}`
                        }
                    }
                }
            });
        });
    });

    document.getElementById('confirmPublish').addEventListener('click', function() {
        const endDate = new Date("{{ \Carbon\Carbon::parse($election->end_date)->toDateString() }}");
        const today = new Date();
        
        if (endDate < today) {
            alert('Results published successfully!');
            $('#publishModal').modal('hide');
        } else {
            alert('You cannot publish results until the election period has ended.');
        }
    });
</script>

</x-layouts.app>