@extends('layouts.app')

@section('title', 'Add Station')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold mb-0">
            Add Station
        </h2>

        <a href="{{ route('admin.stations.index', ['menu' => 'station']) }}"
           class="btn btn-outline-secondary">

            Back to Station List

        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('admin.stations.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">

                    <label for="name"
                           class="form-label fw-semibold">

                        Station Name

                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Station A"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label for="code"
                           class="form-label fw-semibold">

                        Station Code

                    </label>

                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code') }}"
                           class="form-control @error('code') is-invalid @enderror"
                           placeholder="ST-A"
                           required>

                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="form-check mb-4">

                    <input type="hidden"
                           name="is_active"
                           value="0">

                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           id="is_active"
                           class="form-check-input"
                           {{ old('is_active', true) ? 'checked' : '' }}>

                    <label for="is_active"
                           class="form-check-label">

                        Active

                    </label>

                </div>


                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fa-solid fa-plus me-1"></i>
                        Add Station

                    </button>

                    <a href="{{ route('admin.stations.index', ['menu' => 'station']) }}"
                       class="btn btn-outline-secondary">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection