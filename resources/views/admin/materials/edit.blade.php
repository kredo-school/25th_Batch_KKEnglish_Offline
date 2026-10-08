@extends('layouts.app')

@section('title', 'Edit Materials')

@section('content')

<h2 class="fw-bold mb-3">Edit Materials</h2>

@if(session('delete_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('delete_error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
        </button>
    </div>
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


{{-- Update --}}
<form method="POST"
      action="{{ route('admin.materials.update', $material) }}"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    @include('admin.materials._form')

    <button type="submit" class="btn btn-primary">
        Update
    </button>

    <a href="{{ route('admin.materials.index') }}"
       class="btn btn-outline-secondary">
        Cancel
    </a>

</form>


{{-- Delete --}}
<form method="POST"
      action="{{ route('admin.materials.destroy', $material) }}"
      class="mt-2">

    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-outline-danger btn-sm"
            onclick="return confirm('Would you like to delete this material?');">

        <i class="fa-solid fa-trash me-1"></i>
        Delete

    </button>

</form>

@endsection
