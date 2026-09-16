@extends('layouts.app')

@section('content')
<div class="container py-3">

    {{-- ============================================================
         Header
    ============================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 text-bold">ID: {{ $student->id }} {{ $student->user->first_name }} {{ $student->user->last_name }}</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- ============================================================
             Left Column: Profile Form
        ============================================================ --}}
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    {{-- User情報の更新ルートを想定。必要に応じてStudent側の更新ルートに変更してください --}}
                    <form action="{{ route('admin.users.update', $student->user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Profile Image --}}
                        <div class="mb-4 d-flex align-items-center gap-3">
                            <div>
                                @if($student->user->profile_image)
                                    <img src="{{ asset('storage/' . $student->user->profile_image) }}" alt="Profile" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px;">
                                        <i class="bi bi-person-fill fs-2"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm mb-1">Edit</button><br>
                                <button type="button" class="btn btn-outline-danger btn-sm">Delete</button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nickname (English)</label>
                                {{-- ※DBにカラムがないため空値にしています --}}
                                <input type="text" class="form-control" name="nickname" value="" placeholder="Nickname">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="">Please select</option>
                                    <option value="male" @selected($student->user->gender === 'male')>Male</option>
                                    <option value="female" @selected($student->user->gender === 'female')>Female</option>
                                    <option value="other" @selected($student->user->gender === 'other')>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">国籍</label>
                                <input type="text" class="form-control" name="nationality" value="{{ $student->user->nationality }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">名前 (英語)</label>
                                {{-- ※DBにカラムがないため空値にしています --}}
                                <input type="text" class="form-control" name="name_en" value="">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">名前 (姓)</label>
                                <input type="text" class="form-control" name="last_name" value="{{ $student->user->last_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">名前 (名)</label>
                                <input type="text" class="form-control" name="first_name" value="{{ $student->user->first_name }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">メールアドレス</label>
                                <input type="email" class="form-control" name="email" value="{{ $student->user->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">パスワード</label>
                                <input type="password" class="form-control" name="password" placeholder="変更する場合のみ入力">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">電話番号</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ $student->user->{'phone-number'} }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">誕生日</label>
                                <input type="date" class="form-control" name="birthday" value="{{ $student->birthday?->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">住所</label>
                                {{-- ※DBにカラムがないため空値にしています --}}
                                <input type="text" class="form-control" name="address" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">市区町村</label>
                                {{-- ※DBにカラムがないため空値にしています --}}
                                <input type="text" class="form-control" name="city" value="">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">保存する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============================================================
             Right Column: Status & Points
        ============================================================ --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>入会日</span>
                            <strong>{{ $student->created_at?->format('Y-m-d') ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>卒業日</span>
                            {{-- ※DBにカラムがないためプレースホルダーにしています --}}
                            <strong>-</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>残ポイント数</span>
                            <div>
                                <strong class="fs-5">{{ number_format($pointBalance) }}</strong> pt
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>ステータス</span>
                            @if($student->user->status === 'active')
                                <span class="badge bg-success">有効</span>
                            @else
                                <span class="badge bg-secondary">無効</span>
                            @endif
                        </li>
                    </ul>

                    <div class="mt-3 d-grid">
                        <a href="{{ route('admin.students.points.create', $student) }}" class="btn btn-outline-primary btn-sm">ポイントを付与する</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
