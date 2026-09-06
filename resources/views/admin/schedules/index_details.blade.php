@extends('layouts.app')
@section('title', 'Index Details')

@section('content')
<div class="container py-4">
    <h4 class="mb-3">Details: {{ $date }} / {{ $type }}</h4>
    <a href="{{ route('admin.schedules.index', ['week_start' => \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString()]) }}"
       class="btn btn-outline-secondary btn-sm mb-3">Back</a>

    @if($items->isEmpty())
        <p class="text-secondary">No data.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        @foreach(array_keys((array)$items->first()) as $k)
                            <th>{{ $k }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $it)
                        <tr>
                            @foreach((array)$it as $v)
                                <td>{{ $v }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
