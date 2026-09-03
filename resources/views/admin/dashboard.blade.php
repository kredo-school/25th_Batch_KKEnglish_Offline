@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">


        {{-- Dashboard --}}
        <div class="col-12">

            {{-- Hello Header --}}
            <div class="bg-light p-4 mb-4">
                <h2 class="fw-bold mb-1">
                    Hello, Name
                </h2>

                <p class="text-secondary mb-0">
           {{ now()->format('l, F j') }}
        </p>

         <p class="fw-semibold mb-0">
            <i class="fa-regular fa-clock me-1"></i>
            {{ now()->format('H:i') }}
          </p>
            </div>

            {{-- Weekly Summary --}}
            <div class="card mb-4">
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle mb-0">

                            {{-- 週ヘッダ --}}
<tr>
    <th></th>
    @foreach($dashboardRows as $row)
        <th>{{ $row['day_ja'] }}</th>
    @endforeach
</tr>

<tr>
    <th class="text-start">日付</th>
    @foreach($dashboardRows as $row)
        <td>{{ $row['label'] }}</td>
    @endforeach
</tr>

<tr>
    <th class="text-start">全レッスン可能数</th>
    @foreach($dashboardRows as $row)
        <td>{{ $row['capacity'] }}</td>
    @endforeach
</tr>

<tr>
    <th class="text-start">予約数</th>
    @foreach($dashboardRows as $row)
        <td>{{ $row['booked'] }}</td>
    @endforeach
</tr>

<tr>
    <th class="text-start">自動予約生徒数</th>
    @foreach($dashboardRows as $row)
        <td>{{ $row['auto_booked'] }}</td>
    @endforeach
</tr>

<tr>
    <th class="text-start">先生の出勤数</th>
    @foreach($dashboardRows as $row)
        <td>{{ $row['working_teachers'] }}</td>
    @endforeach
</tr>

                        </table>

                    </div>

                </div>
            </div>

            {{-- Information --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        お知らせ
                    </h5>
@if($announcements->isEmpty())
    <p class="text-secondary mb-0">お知らせはありません。</p>
@else
    <ul class="list-group list-group-flush">
        @foreach($announcements as $item)
            <li class="list-group-item px-0">
                <div class="fw-semibold">{{ $item->title }}</div>
                <div class="small text-secondary">{{ \Illuminate\Support\Str::limit($item->body, 120) }}</div>
            </li>
        @endforeach
    </ul>
@endif
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
