@extends('layouts.app')
@section('title','Register Materials')
@section('content')
<h2 class="fw-bold mb-3">Register Materials</h2>
@if($errors->any()) <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
<form method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.materials._form')
    <button class="btn btn-primary">Register</button>
    <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary">Back</a>
</form>
@endsection
