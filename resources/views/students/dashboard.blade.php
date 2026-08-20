@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">

        {{-- Dashboard --}}
      <div class="col-12">

         {{-- Hello Header --}}
        <div class="bg-light p-4 mb-4 d-flex justify-content-between align-items-center">

         {{-- 名前・日付 --}}
      <div>
        <h2 class="fw-bold mb-1">
            Hello, {{ auth()->user()->first_name }}
            {{-- ダミー　ヘッダーができたら、下記をヘッダーに持って行ってください。 --}}
            <a href="{{ route('student.profile') }}" class="btn btn-link">Profile</a>
        </h2>

        <p class="text-secondary mb-0">
           {{ now()->format('l, F j') }}
        </p>

         <p class="fw-semibold mb-0">
            <i class="fa-regular fa-clock me-1"></i>
            {{ now()->format('H:i') }}
          </p>

      </div>

    {{-- レッスン予約ボタン --}}
    <a href="#" class="btn btn-primary">
        レッスンを予約
    </a>

</div>

            {{-- Main --}}
            <div class="row g-3">

                {{-- 左側：本日のレッスン --}}
                <div class="col-md-7">

                    <div class="card">
                        <div class="card-body">

                            {{-- タイトル＋日付切り替え --}}
                            <div class="d-flex align-items-center gap-4 mb-4">

                                <h5 class="mb-0">
                                    本日のレッスン
                                </h5>

                                <div class="d-flex align-items-center gap-3">

                                    <a href="#"
                                       class="text-dark text-decoration-none fw-bold">
                                        &lt; 前日
                                    </a>

                                    <span class="fw-bold">
                                        8月19日
                                    </span>

                                    <a href="#"
                                       class="text-dark text-decoration-none fw-bold">
                                        翌日 &gt;
                                    </a>

                                </div>
                            </div>

                            {{-- レッスン1 --}}
                            <div class="border rounded p-3 mb-3">
                                <p class="mb-1 fw-bold">
                                    10:00 - 10:50
                                </p>

                                <p class="mb-0">
                                    Sarah Mitchell
                                </p>
                            </div>

                            {{-- レッスン2 --}}
                            <div class="border rounded p-3">
                                <p class="mb-1 fw-bold">
                                    14:00 - 14:50
                                </p>

                                <p class="mb-0">
                                    Sarah Mitchell
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- 右側 --}}
                <div class="col-md-5">

                    {{-- ポイント・レベル --}}
                    <div class="row g-3 mb-3">

                        {{-- Point --}}
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    保有ポイント
                                </div>
                            </div>
                        </div>

                        {{-- Level --}}
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    現在のレベル
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Information --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-0">
                                お知らせ
                            </h5>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@endsection
