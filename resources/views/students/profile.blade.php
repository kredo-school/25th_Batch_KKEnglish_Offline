@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
 <div class="container my-4">
    {{-- タブ --}}
    <div class="d-flex mb-0">
        <a href="{{ route('student.profile') }}"
           class="px-4 py-3 border border-bottom-0 bg-white text-dark text-decoration-none">
            <i class="fa-regular fa-id-badge me-2"></i>
            My Account

            @if($user->profile_image)
                <img
                    src="{{ asset('storage/' . $user->profile_image) }}"
                    alt="Profile Image"
                    width="120"
                    height="120"
                    class="rounded-circle object-fit-cover"
                >
            @else
                <i class="fa-solid fa-circle-user fa-2x" style="width:120, height: 120"></i>
            @endif
        </a>
    </div>


    <div class="col-10 border bg-white p-3">

        {{-- 説明 --}}
        <div class="p-4 border-bottom">
            Please enter the following fields.
            <span class="text-danger">
                ※レッスン受講される生徒様ご本人の情報を入力ください。
            </span>
        </div>


        <form action="{{ route('student.profile.update') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PATCH')


            {{-- English Name --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Name (in English)
                    <span class="text-danger">*</span>
                </div>

                <div class="col-md-9 p-3">
                    <div class="row g-2">

                        <div class="col-md-4">
                            <input
                                type="text"
                                name="first_name"
                                class="form-control"
                                value="{{ old('first_name', $user->first_name) }}"
                                placeholder="First Name"
                            >
                        </div>
                        @error('first_name')
                            <div class="col-md-12 text-danger">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="col-md-4">

                            <input
                                type="text"
                                name="last_name"
                                class="form-control"
                                value="{{ old('last_name', $user->last_name) }}"
                                placeholder="Last Name"
                            >
                        </div>
                        @error('last_name')
                            <div class="col-md-12 text-danger">
                                    {{ $message }}
                            </div>
                        @enderror

                    </div>
                </div>
            </div>


            {{-- Email --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Email Address
                </div>

                <div class="col-md-9 p-3">
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                    >
                    @error('email')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Password --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Password
                </div>

                <div class="col-md-9 p-3">
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="変更する場合のみ入力してください"
                    >
                    @error('password')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Phone Number --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Phone Number
                </div>

                <div class="col-md-9 p-3">
                    <input
                        type="text"
                        name="phone_number"
                        class="form-control"
                        value="{{ old('phone_number', $user->phone_number) }}"
                        placeholder="090-0000-0000"
                    >
                    @error('phone_number')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Nationality --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Nationality
                </div>

                <div class="col-md-9 p-3">
                    <select name="nationality" class="form-select">

                        <option value="">Select nationality</option>

                        <option value="Japanese"
                            {{ old('nationality', $user->nationality) == 'Japanese' ? 'selected' : '' }}>
                            Japanese
                        </option>

                        <option value="Filipino"
                            {{ old('nationality', $user->nationality) == 'Filipino' ? 'selected' : '' }}>
                            Filipino
                        </option>

                        <option value="Korean"
                            {{ old('nationality', $user->nationality) == 'Korean' ? 'selected' : '' }}>
                            Korean
                        </option>

                        <option value="Chinese"
                            {{ old('nationality', $user->nationality) == 'Chinese' ? 'selected' : '' }}>
                            Chinese
                        </option>

                    </select>
                    @error('nationality')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Gender --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Gender
                </div>

                <div class="col-md-9 p-3">

                    <select name="gender" class="form-select">

                        <option value="">Select gender</option>

                        <option value="male"
                            {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                            Male
                        </option>

                        <option value="female"
                            {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                            Female
                        </option>

                        <option value="other"
                            {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                            Other
                        </option>

                    </select>
                    @error('gender')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Birthday --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Birthday
                </div>

                <div class="col-md-9 p-3">

                    <input
                        type="date"
                        name="birthday"
                        class="form-control"
                        value="{{ old('birthday', optional($user->student)->birthday) }}"
                    >
                    @error('birthday')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            {{-- Profile Image --}}
            <div class="row border-bottom">
                <div class="col-md-3 bg-light p-3 text-md-end">
                    Profile Image
                </div>

                <div class="col-md-9 p-3">
                    <input
                        type="file"
                        name="profile_image"
                        class="form-control"
                        >
                    @error('profile_image')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>


            {{-- Button --}}
            <div class="p-4 text-center">

                <a href="{{ route('student.profile') }}"
                   class="btn btn-secondary me-2">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-5">
                    Update Profile
                </button>

            </div>

        </form>

    </div>

</div>
@endsection
