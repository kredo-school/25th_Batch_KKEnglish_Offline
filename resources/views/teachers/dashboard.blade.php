@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">


        {{-- Dashboard --}}
        <div class="col-12">

            {{-- Hello Header --}}
            <div class="bg-light p-4 mb-4">
                <h2 class="fw-bold mb-1">
                    Hello, Name
                     <a href="{{ route('teacher.profile') }}" class="btn btn-link">Profile</a>
                </h2>

                  <p class="text-secondary mb-0">
           {{ now()->format('l, F j') }}
        </p>

         <p class="fw-semibold mb-0">
            <i class="fa-regular fa-clock me-1"></i>
            {{ now()->format('H:i') }}
          </p>

            </div>


            {{-- 本日のレッスン --}}
            <div class="card">
                <div class="card-body">

                    {{-- タイトル・日付切り替え --}}
                    <div class="d-flex  align-items-center gap-4 mb-4">

                        <h5 class="mb-0">
                            本日のレッスン
                        </h5>

                        <div class="d-flex align-items-center gap-4">

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


                    {{-- レッスン一覧 --}}
                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>
                                <tr>
                                    <th>時間</th>
                                    <th>生徒名</th>
                                    <th>カリキュラム</th>
                                    <th>教室</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                {{-- レッスン１--}}
                                <tr>
                                    <td>10:00</td>

                                    <td>
                                        <i class="fa-solid fa-circle-user me-2"></i>
                                        山田 花子
                                    </td>

                                    <td>
                                        日常英会話 入門
                                    </td>

                                    <td>
                                        Room A
                                    </td>

                                    <td>
                                        <a href="#"
                                           class="btn btn-outline-primary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                 {{-- レッスン２--}}

                                <tr>
                                    <td>14:00</td>

                                    <td>
                                        <i class="fa-solid fa-circle-user me-2"></i>
                                        佐藤 太郎
                                    </td>

                                    <td>
                                        Business English
                                    </td>

                                    <td>
                                        Room B
                                    </td>

                                    <td>
                                        <a href="#"
                                           class="btn btn-outline-primary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

@endsection
