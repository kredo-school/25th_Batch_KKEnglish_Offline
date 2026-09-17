@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="h4 mb-4">Season Period Settings</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="row">
        <!-- 新規追加フォーム -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-light fw-bold">Add New Period</div>
                <div class="card-body">
                    <form action="{{ route('admin.season-periods.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Season Type</label>
                            <select name="season_type" class="form-select" required>
                                <option value="normal">Normal (通常期)</option>
                                <option value="busy">Busy (混雑期)</option>
                                <option value="quiet">Quiet (閑散期)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 登録済み期間一覧 -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Season Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $typeLabels = ['normal' => 'Normal', 'busy' => 'Busy', 'quiet' => 'Quiet'];
                                $typeColors = ['normal' => 'bg-success', 'busy' => 'bg-danger', 'quiet' => 'bg-info'];
                            @endphp
                            @forelse($periods as $p)
                                <tr>
                                    <td>{{ $p->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $p->end_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge {{ $typeColors[$p->season_type] }}">
                                            {{ $typeLabels[$p->season_type] }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.season-periods.destroy', $p) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this period?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-muted">No periods have been registered. (Periods without settings are treated as Normal by default.)</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection