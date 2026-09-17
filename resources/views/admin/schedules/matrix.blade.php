@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Operational Status</h1>
        <div class="d-flex gap-2">
            {{-- 1週間前/後へのナビゲーション --}}
            <a href="{{ route('admin.schedules.matrix', ['start_date' => $start->copy()->subDays(7)->toDateString()]) }}" class="btn btn-outline-secondary btn-sm">＜ Prev</a>
            <a href="{{ route('admin.schedules.matrix', ['start_date' => $start->copy()->addDays(7)->toDateString()]) }}" class="btn btn-outline-secondary btn-sm">Next ＞</a>
        </div>
    </div>

    <div class="card mb-3 bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">Information Summary</p>
                    <p class="mb-1">All Capacity: {{ $totalCapacity }}</p>
                    <p class="mb-1">Booked: {{ $totalBooked }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead style="background-color: #fce4d6;">
                        <tr>
                            <th class="py-3" style="width: 100px;"></th>
                            @php $weekMap = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat']; @endphp
                            @foreach($dates as $d)
                                <th class="py-2">
                                    <div class="mb-1">{{ $weekMap[$d->dayOfWeek] }}</div>
                                    <a href="{{ route('admin.schedules.matrix_details', ['date' => $d->toDateString()]) }}" class="text-decoration-none fw-bold">
                                        {{ $d->format('m/d') }}
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hours as $hour)
                            <tr>
                                <th class="bg-light">{{ sprintf('%02d:00', $hour) }}</th>
                                @foreach($dates as $d)
                                    @php $cell = $matrix[$hour][$d->toDateString()]; @endphp
                                    <td style="background-color: {{ $cell['bg'] }};" class="{{ $cell['text'] }} fw-semibold">
                                        @if($cell['status'] !== 'No Slot')
                                            {{ $cell['status'] }}
                                            {{-- 確認用に数値を小さく表示したい場合は以下のコメントアウトを外してください --}}
                                            {{-- <br><small class="fw-normal">({{ $cell['booked'] }}/{{ $cell['capacity'] }})</small> --}}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection