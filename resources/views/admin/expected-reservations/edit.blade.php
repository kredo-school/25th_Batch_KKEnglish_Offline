@extends('layouts.app')

@section('content')
<div class="container py-3">

    <h1 class="h4 mb-3">Expected Reservations</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.expected-reservations.update') }}">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs mb-4" id="seasonTabs" role="tablist">
            @foreach($seasons as $key => $label)
                <li class="nav-item bg-secondary-subtle" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            id="{{ $key }}-tab" data-bs-toggle="tab"
                            data-bs-target="#{{ $key }}-pane"
                            type="button" role="tab"
                            aria-controls="{{ $key }}-pane"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="seasonTabsContent">
            @foreach($seasons as $key => $label)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="{{ $key }}-pane" role="tabpanel"
                     aria-labelledby="{{ $key }}-tab" tabindex="0">

                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Weekday</th>
                                        <th>Weekend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($times as $time)
                                        <tr>
                                            <th class="bg-light">{{ $time }}</th>
                                            <td>
                                                <input type="number" min="0" class="form-control text-center"
                                                       name="expected[{{ $key }}][weekday][{{ $time }}]"
                                                       value="{{ $settings[$key]['weekday'][$time] ?? 0 }}">
                                            </td>
                                            <td>
                                                <input type="number" min="0" class="form-control text-center"
                                                       name="expected[{{ $key }}][weekend][{{ $time }}]"
                                                       value="{{ $settings[$key]['weekend'][$time] ?? 0 }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4">Save Settings</button>
                <a href="{{ route('admin.schedules.matrix_details', ['date' => request('date', now()->toDateString())]) }}"
                class="btn btn-outline-secondary btn-sm"> <i class="fa-solid fa-angles-left"></i> Back </a>
            </div>
        </div>
    </form>
</div>
@endsection
