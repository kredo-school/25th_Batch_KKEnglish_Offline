@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <form action="{{ route('student.profile.update') }}" method="POST">
                <div class="col">
                    @csrf
                    @method('PATCH')
                    <h1>Profile</h1>
                    <p>First Name: {{ $user->first_name }}</p>
                    <p>Last Name: {{ $user->last_name }}</p>
                    <p>Email: {{ $user->email }}</p>
                    <p>Phone Number: {{ $user->phone_number }}</p>
                    <p>Nationality: {{ $user->nationality }}</p>
                    <p>Gender: {{ $user->gender }}</p>

                </div>
                    @if ($user->student)
                        <p>Birthday: {{ $user->student->birthday }}</p>
                    @endif
                    <p>
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    </p>
                <div class="col">


                </div>


            </form>




        </div>

    </div>
@endsection
