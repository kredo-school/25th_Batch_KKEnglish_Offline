@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Details ({{ $date->format('Y-m-d') }})</h1>
        <a href="{{ route('admin.schedules.matrix', ['start_date' => $date->copy()->startOfWeek()->toDateString()]) }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-primary text-white" style="background-color: #4b8bdf;">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Expected Reservations</th>
                            <th>Spare Capacity</th>
                            <th>Required Teachers</th>
                            <th>Assigned Teachers</th>
                            <th>Difference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $weekMap = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat']; @endphp
                        @foreach($intervals as $item)
                            <tr>
                                <td>{{ $date->format('Y-m-d') }}</td>
                                <td>{{ $weekMap[$date->dayOfWeek] }}</td>
                                <td>{{ $item['start'] }}</td>
                                <td>{{ $item['end'] }}</td>
                                
                                <td class="bg-warning bg-opacity-10">{{ $item['booked'] }}</td>
                                <td class="bg-warning bg-opacity-10">{{ $item['spare'] }}</td>
                                <td class="bg-warning bg-opacity-10 fw-bold">{{ $item['required'] }}</td>
                                
                                <td class="bg-success bg-opacity-10 fw-bold">{{ $item['capacity'] }}</td>
                                
                                @if($item['diff'] >= 0)
                                    <td class="text-success fw-bold">{{ $item['diff'] }}</td>
                                    <td class="text-success fw-bold">Sufficient</td>
                                @else
                                    <td class="text-danger fw-bold">{{ $item['diff'] }}</td>
                                    <td class="text-danger fw-bold">Insufficient</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection