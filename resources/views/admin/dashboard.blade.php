@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">

           {{-- admin Sidebar --}}
        <div class="col-md-3 col-lg-2 p-0">
            @include('components.sidebars.admin')
        </div>

        {{-- Dashboard --}}
        <div class="col-12">

            {{-- Hello Header --}}
            <div class="bg-light p-4 mb-4">
                <h2 class="fw-bold mb-1">
                    Hello, Name
                </h2>

                <p class="text-secondary mb-0">
           {{ now()->format('l, F j') }}
        </p>

         <p class="fw-semibold mb-0">
            {{ now()->format('H:i') }}
          </p>
            </div>

            {{-- Weekly Summary --}}
            <div class="card mb-4">
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle mb-0">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>月</th>
                                    <th>火</th>
                                    <th>水</th>
                                    <th>木</th>
                                    <th>金</th>
                                    <th>土</th>
                                    <th>日</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th class="text-start">日付</th>
                                    <td>8/10</td>
                                    <td>8/11</td>
                                    <td>8/12</td>
                                    <td>8/13</td>
                                    <td>8/14</td>
                                    <td>8/15</td>
                                    <td>8/16</td>
                                </tr>

                                <tr>
                                    <th class="text-start">全レッスン可能数</th>
                                    <td>20</td>
                                    <td>20</td>
                                    <td>18</td>
                                    <td>22</td>
                                    <td>20</td>
                                    <td>10</td>
                                    <td>8</td>
                                </tr>

                                <tr>
                                    <th class="text-start">予約数</th>
                                    <td>12</td>
                                    <td>15</td>
                                    <td>11</td>
                                    <td>14</td>
                                    <td>16</td>
                                    <td>7</td>
                                    <td>5</td>
                                </tr>

                                <tr>
                                    <th class="text-start">自動予約生徒数</th>
                                    <td>3</td>
                                    <td>4</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>5</td>
                                    <td>2</td>
                                    <td>1</td>
                                </tr>

                                <tr>
                                    <th class="text-start">先生の出勤数</th>
                                    <td>5</td>
                                    <td>5</td>
                                    <td>4</td>
                                    <td>5</td>
                                    <td>5</td>
                                    <td>3</td>
                                    <td>2</td>
                                </tr>
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

            {{-- Information --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        お知らせ
                    </h5>

                </div>
            </div>

        </div>

    </div>
</div>

@endsection