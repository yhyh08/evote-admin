<div>
    <x-layouts.app>
        <main>
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header py-4 px-3">
                                <div class="d-flex flex-row justify-content-between">
                                    <div>
                                        <h5 class="mb-0">Nomination</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Steps -->
                            <div class="mb-5">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="step active">
                                        <span class="number">01</span>
                                        <span class="text">Nominee</span>
                                    </div>
                                    <div class="step">
                                        <span class="number">02</span>
                                        <span class="text">Candidate Info</span>
                                    </div>
                                    <div class="step">
                                        <span class="number">03</span>
                                        <span class="text">Manifesto</span>
                                    </div>
                                    <div class="step">
                                        <span class="number">04</span>
                                        <span class="text">Document</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- Nominees Section -->
                            @foreach($nominations as $index => $nomination)
                                <div class="nominee-section mb-5">
                                    <h4>{{ $index === 0 ? 'First' : 'Second' }} Nominee</h4>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Name:</label>
                                                <p class="form-control-static">{{ $nomination->nominee_name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Phone Number:</label>
                                                <p class="form-control-static">{{ $nomination->nominee_phone }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Email:</label>
                                                <p class="form-control-static">{{ $nomination->nominee_email }}</p>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Reason:</label>
                                                <p class="form-control-static">{{ $nomination->reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-4 px-4 pb-4">
                                <button class="btn btn-secondary" wire:click="back">Back</button>
                                <button class="btn btn-primary" wire:click="next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </x-layouts.app>
</div>