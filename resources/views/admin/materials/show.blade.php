@extends('layouts.app')

@section('title', 'Material Details')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Material Details</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-start gap-4">
                <img
                    src="{{ $material->cover_image ? asset('storage/' . $material->cover_image) : asset('images/no-image.png') }}"
                    alt="{{ $material->name }}"
                    width="140"
                    height="140"
                    class="rounded border"
                    style="object-fit: cover;"
                >

                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-3">{{ $material->name }}</h4>

                    <p class="mb-1"><strong>ID:</strong> {{ $material->material_id }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ $material->status }}</p>
                    <p class="mb-1"><strong>Level:</strong> {{ $material->level ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Target Level:</strong> {{ $material->target_level ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Duration:</strong> {{ $material->duration ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Printed Textbook:</strong> {{ $material->printed_textbook ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Available Materials:</strong> {{ $material->available_materials ?? 'N/A' }}</p>
                </div>
            </div>

            <hr>
            <h6 class="fw-bold">Description</h6>
            <p class="mb-0">{{ $material->description ?? 'No description' }}</p>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-outline-primary btn-sm">Edit</a>

                @if($material->status === 'active')
                    <form method="POST" action="{{ route('admin.materials.suspend', $material) }}" onsubmit="return confirm('この教材を一時停止しますか？');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning btn-sm">Suspend</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('この教材を削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                </form>

                <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
