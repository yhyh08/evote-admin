<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h4>Result</h4>
                            <div class="d-flex">
                                <div class="search-bar me-3">
                                    <input type="text" class="form-control" placeholder="Search">
                                </div>
                                <button class="btn btn-primary me-2">
                                    <i class="fas fa-download"></i> Download
                                </button>
                                <button class="btn btn-success">
                                    <i class="fas fa-globe"></i> Publish
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="mb-4">Election {{ $election->id }}</h5>
                            
                            <h6 class="mb-3">Candidates</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Profile</th>
                                            <th>Candidate</th>
                                            <th>Ballot</th>
                                            <th>Ballot Percentages (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($election->candidates as $candidate)
                                        <tr>
                                            <td>
                                                <div class="avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </td>
                                            <td>{{ $candidate->name }}</td>
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
                                                    <span>{{ number_format($candidate->percentage, 0) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-5">
                                <h6 class="mb-3">Statistics</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Add your bar chart here -->
                                        <div id="barChart" style="height: 300px;"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Add your pie chart here -->
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
        // Initialize charts when the document is ready
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