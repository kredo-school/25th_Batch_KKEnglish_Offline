@extends('layouts.app')

@section('title', 'Teaching Materials')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">Teaching Materials</h2>

    {{-- 教材一覧 --}}
    <div class="row g-4">

        @foreach ($materials as $material)

            <div class="col-md-6 col-lg-4">
                <div class="card h-100">

                    <div class="card-body d-flex align-items-center">

                        {{-- 教材画像 --}}
                        <img src="{{ asset('storage/' . $material->cover_image) }}"
                             alt="{{ $material->name }}"
                             width="90"
                             height="90"
                             class="rounded me-3"
                             style="object-fit: cover;">

                        <div>

                            {{-- レベル --}}
                            <span class="badge bg-primary mb-2">
                                {{ $material->level }}
                            </span>

                            {{-- 教材名 --}}
                            <h5 class="fw-bold mb-2">
                                {{ $material->name }}
                            </h5>

                            {{-- 説明 --}}
                            <p class="text-secondary mb-0">
                                {{ $material->description }}
                            </p>

                        </div>

                    </div>

                </div>
            </div>

        @endforeach

    </div>

</div>

@endsection