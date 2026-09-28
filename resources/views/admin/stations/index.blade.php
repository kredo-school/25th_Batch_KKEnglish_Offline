@extends('layouts.app')

@section('title', 'Station List')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">Station List</h2>
            <p class="text-muted mb-0">
                Manage stations used for lessons.
            </p>
        </div>

        <a href="{{ route('admin.stations.create', ['menu' => 'station']) }}"
           class="btn btn-primary">

            <i class="fa-solid fa-plus me-1"></i>
            Add Station

        </a>

    </div>


    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- Validation Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($stations as $station)

                            <tr>

                                <td class="ps-4">
                                    {{ $station->id }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $station->name }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $station->code }}
                                    </span>
                                </td>

                                <td>

                                    @if ($station->is_active)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td class="text-end pe-4">

                                    <a href="{{ route('admin.stations.edit', [
                                        'station' => $station,
                                        'menu' => 'station'
                                    ]) }}"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="fa-solid fa-pen-to-square me-1"></i>
                                        Edit

                                    </a>


                                    <form action="{{ route('admin.stations.destroy', $station) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('このStationを削除しますか？');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">

                                            <i class="fa-solid fa-trash me-1"></i>
                                            Delete

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center py-5 text-muted">

                                    No stations found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection