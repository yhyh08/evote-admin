<div>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Election Results</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('result') }}" class="btn btn-secondary">Back</a>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Election Details</h5>
                                    <p><strong>Topic:</strong> {{ $election->election_topic }}</p>
                                    <p><strong>Description:</strong> {{ $election->description }}</p>
                                    <p><strong>Period:</strong> 
                                        {{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div> 