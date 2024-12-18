<!DOCTYPE html>
<html>
<head>
    <title>Election Results</title>
    <style>
        /* Add any necessary styles for the PDF */
        body { font-family: DejaVu Sans, sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $election->election_topic }}</h1>
    <p>Period: {{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }} - 
       {{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}</p>

    <h2>Candidates Results</h2>
    @foreach($election->grouped_candidates as $position => $candidates)
        <h3>{{ $position }}</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Ballot</th>
                    <th>Ballot Percentages (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $candidate)
                <tr>
                    <td>{{ $candidate->candidate_name }}</td>
                    <td>{{ $candidate->votes_count }}</td>
                    <td>{{ number_format($candidate->percentage, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html> 