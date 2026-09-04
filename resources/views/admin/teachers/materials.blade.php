@extends('layouts.app')

@section('title', 'Teacher Materials')

@section('content')
<div class="container">
    <h3 class="mb-3">Subject Assignment: {{ $teacher->name ?? 'Teacher' }}</h3>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teachers.materials.update', $teacher) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                @forelse($materials as $material)
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="material_ids[]"
                            value="{{ $material->id }}"
                            id="material_{{ $material->id }}"
                            {{ in_array($material->id, $selectedMaterialIds, true) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="material_{{ $material->id }}">
                            {{ $material->name }}
                        </label>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No subjects have been registered.</p>
                @endforelse
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save</button>
    </form>
</div>
@endsection
