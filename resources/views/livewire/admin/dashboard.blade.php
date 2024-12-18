<div>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Election</p>
                    <h5 class="font-weight-bolder mb-0">
                      2,000
                      {{-- <span class="text-success text-sm font-weight-bolder">+55%</span> --}}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Nomination</p>
                    <h5 class="font-weight-bolder mb-0">
                      2,000
                      {{-- <span class="text-success text-sm font-weight-bolder">+3%</span> --}}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Users</p>
                    <h5 class="font-weight-bolder mb-0">
                      2,000
                      {{-- <span class="text-danger text-sm font-weight-bolder">-2%</span> --}}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 mt-4">
                        <div class="card-header py-4 px-3">
                            <div class="d-flex flex-row justify-content-between">
                                <div>
                                    <h5 class="mb-0">Election Results</h5>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($elections as $index => $election)
                                            <tr>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $index + 1 }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->election_topic }}</p>
                                                </td>
                                                <td class="ps-3">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $election->description }}</p>
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
</div>