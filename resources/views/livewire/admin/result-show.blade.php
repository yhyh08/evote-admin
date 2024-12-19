<div>
    <main class="main-content position-relative h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $election->election_topic }}</h4>
                                <p class="text-sm text-muted mb-0">
                                    Period: {{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="d-flex">
                                <a href="javascript:history.back()" class="btn btn-secondary btn-back" type="button">
                                    <i class="fa-solid fa-chevron-left me-2"></i>Back
                                </a>
                                
                                <a href="{{ route('result.show', ['election' => $election->id]) }}" class="btn btn-primary">
                                    View Results
                                </a>

                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#publishModal">
                                    <i class="fas fa-globe"></i> Publish
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="mb-3">Candidates Results</h6>
                            @forelse($election->grouped_candidates as $position => $candidates)
                                <h5 class="text-primary mt-4 mb-3">{{ $position }}</h5>
                                <table class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>Profile</th>
                                            <th>Candidate</th>
                                            <th>Ballot</th>
                                            <th>Ballot Percentages (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($candidates as $candidate)
                                        <tr>
                                            <td>
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
                                            <td>
                                                <p class="text-sm mb-0">{{ $candidate->candidate_name }}</p>
                                                <p class="text-xs text-muted mb-0">{{ $candidate->position }}</p>
                                            </td>
                                            <td>{{ $candidate->votes_count }}</td>
                                            <td>
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

        // Create pie charts for each position
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
        // Check if the election period has ended
        const endDate = new Date("{{ \Carbon\Carbon::parse($election->end_date)->toDateString() }}");
        const today = new Date();
        
        if (endDate < today) {
            // Logic to publish the results
            alert('Results published successfully!');
            // Close the modal
            $('#publishModal').modal('hide');
        } else {
            alert('You cannot publish results until the election period has ended.');
        }
    });
</script>