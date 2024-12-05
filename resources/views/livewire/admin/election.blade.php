@extends('layouts.app')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Election Management</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Election Name</th>
                                        <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Date</th>
                                        <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Status</th>
                                        <th class="text-left text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Add your election data here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 