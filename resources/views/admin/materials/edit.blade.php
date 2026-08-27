@extends('layouts.app')
@section('title','Edit Materials')
@section('content')
<h2 class="fw-bold mb-3">Edit Materials</h2>
@if($errors->any()) <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
<form method="POST" action="{{ route('admin.materials.update', $material) }}">
    @csrf
    @method('PUT')
    @include('admin.materials._form')
    <button class="btn btn-primary">Update</button>
    <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary">Back</a>
</form>
@endsection