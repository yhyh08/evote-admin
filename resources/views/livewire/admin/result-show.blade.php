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
                                <button class="btn btn-primary me-2">
                                    <i class="fas fa-download"></i> Download
                                </button>
                                <button class="btn btn-success">
                                    <i class="fas fa-globe"></i> Publish
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="mb-3">Candidates Results</h6>
                            @foreach($election->grouped_candidates as $position => $candidates)
                                <h5 class="text-primary mt-4 mb-3">{{ $position }}</h5>
                                <table class="table w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-left p-2">Profile</th>
                                            <th class="text-left p-2">Candidate</th>
                                            <th class="text-left p-2">Ballot</th>
                                            <th class="text-left p-2">Ballot Percentages (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($candidates as $candidate)
                                        <tr>
                                            <td class="text-left p-2">
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
                                            <td class="text-left p-2">
                                                <p class="text-sm mb-0">{{ $candidate->candidate_name }}</p>
                                                <p class="text-xs text-muted mb-0">{{ $candidate->position }}</p>
                                            </td>
                                            <td class="text-left p-2">{{ $candidate->votes_count }}</td>
                                            <td class="text-left p-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 7px;">
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
                            @endforeach

                            <div class="mt-5">
                                <h6 class="mb-3">Statistics</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div id="barChart" style="height: 300px;"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="pieChart" style="height: 300px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bar Chart
            var barOptions = {
                series: [{
                    name: 'Votes',
                    data: [{{ implode(',', $election->candidates->pluck('votes_count')->toArray()) }}]
                }],
                chart: {
                    type: 'bar',
                    height: 300
                },
                xaxis: {
                    categories: {!! json_encode($election->candidates->pluck('name')->toArray()) !!}
                }
            };
            var barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
            barChart.render();

            // Pie Chart
            var pieOptions = {
                series: [{{ implode(',', $election->candidates->pluck('votes_count')->toArray()) }}],
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: {!! json_encode($election->candidates->pluck('name')->toArray()) !!}
            };
            var pieChart = new ApexCharts(document.querySelector("#pieChart"), pieOptions);
            pieChart.render();
        });
    </script>
    @endpush

</div>