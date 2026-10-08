@extends('layouts.app')

@section('title', 'Edit Materials')

@section('content')

<h2 class="fw-bold mb-3">Edit Materials</h2>

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


{{-- Delete Error Modal --}}
@if(session('delete_error'))

    <div class="modal fade show"
         id="deleteErrorModal"
         tabindex="-1"
         aria-labelledby="deleteErrorModalLabel"
         aria-modal="true"
         role="dialog"
         style="display: block;">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="deleteErrorModalLabel">
                        Cannot Delete Material
                    </h5>

                    <button type="button"
                            class="btn-close"
                            onclick="closeDeleteErrorModal()"
                            aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <p class="mb-0">
                        {{ session('delete_error') }}
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            onclick="closeDeleteErrorModal()">
                        Close
                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- Modal Background --}}
    <div class="modal-backdrop fade show"></div>

@endif

@endsection


@if(session('delete_error'))

<script>
function closeDeleteErrorModal() {
    const modal = document.getElementById('deleteErrorModal');
    const backdrop = document.querySelector('.modal-backdrop');

    if (modal) {
        modal.remove();
    }

    if (backdrop) {
        backdrop.remove();
    }

    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
}
</script>

@endif
