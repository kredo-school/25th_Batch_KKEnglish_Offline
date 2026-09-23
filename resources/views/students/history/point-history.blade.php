@extends('layouts.app')

@section('title', 'Point History')

@section('content')

<div class="container-fluid">

    {{-- Back --}}
    <div class="mb-3">

        <a
            href="{{ route('students.history.index') }}"
            class="btn btn-outline-secondary btn-sm"
        >
            <i class="fa-solid fa-angles-left me-1"></i>
            Back
        </a>

    </div>

    {{-- ===============================
         Title
    ================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Point History
            </h2>

            <p class="text-secondary mb-0">
                View your point usage and refunds.
            </p>
        </div>


        {{-- Current Points --}}
        <div class="border rounded px-3 py-2">

            <span class="text-secondary small me-2">
                Current Points
            </span>

            <span class="fw-bold">
                {{ number_format($student->point_balance ?? 0) }} pt
            </span>

        </div>

    </div>


    {{-- ===============================
         Point Transactions
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Point Transactions
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    {{-- ===============================
                         Header
                    ================================ --}}
                    <thead class="table-light">

                        <tr>

                            <th class="px-4 py-3">
                                Date
                            </th>

                            <th class="py-3">
                                Type
                            </th>

                            <th class="py-3">
                                Teacher
                            </th>

                            <th class="py-3">
                                Material
                            </th>

                            <th
                                class="py-3 text-center"
                                style="width: 110px;"
                            >
                                Points
                            </th>

                        </tr>

                    </thead>


                    {{-- ===============================
                         Body
                    ================================ --}}
                    <tbody>

                        @forelse ($pointTransactions as $transaction)

                            <tr>


                                {{-- ===============================
                                     Date
                                ================================ --}}
                                <td class="px-4">

                                    <div class="fw-semibold">

                                        {{
                                            $transaction
                                                ->created_at
                                                ?->format('M d, Y')
                                            ?? '-'
                                        }}

                                    </div>

                                    <small class="text-secondary">

                                        {{
                                            $transaction
                                                ->created_at
                                                ?->format('H:i')
                                            ?? ''
                                        }}

                                    </small>

                                </td>


                                {{-- ===============================
                                     Type
                                ================================ --}}
                                <td>

                                    @if (
                                        $transaction
                                            ->transactionType
                                            ?->type_code
                                        === 'reservation_use'
                                    )

                                        <span class="badge bg-light text-dark border">
                                            Consumed
                                        </span>

                                    @elseif (
                                        $transaction
                                            ->transactionType
                                            ?->type_code
                                        === 'reservation_refund'
                                    )

                                        <span class="badge bg-light text-dark border">
                                            Refund
                                        </span>

                                    @else

                                        <span class="badge bg-light text-dark border">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- ===============================
                                     Teacher
                                ================================ --}}
                                <td>

                                    @if ($transaction->reservation)

                                        <div class="d-flex align-items-center">

                                            {{-- Teacher Image --}}
                                            @if (
                                                $transaction
                                                    ->reservation
                                                    ->teacher
                                                    ?->user
                                                    ?->profile_image
                                            )

                                                <img
                                                    src="{{ $transaction->reservation->teacher->user->profile_image }}"
                                                    alt="Teacher"
                                                    width="40"
                                                    height="40"
                                                    class="rounded-circle me-2"
                                                    style="object-fit: cover;"
                                                >

                                            @else

                                                <div
                                                    class="
                                                        rounded-circle
                                                        bg-light
                                                        d-flex
                                                        justify-content-center
                                                        align-items-center
                                                        text-secondary
                                                        me-2
                                                    "
                                                    style="
                                                        width: 40px;
                                                        height: 40px;
                                                    "
                                                >
                                                    <i class="fa-solid fa-user"></i>
                                                </div>

                                            @endif


                                            {{-- Teacher Name --}}
                                            <span class="fw-semibold">

                                                {{
                                                    $transaction
                                                        ->reservation
                                                        ->teacher
                                                        ?->user
                                                        ?->first_name
                                                    ?? ''
                                                }}

                                                {{
                                                    $transaction
                                                        ->reservation
                                                        ->teacher
                                                        ?->user
                                                        ?->last_name
                                                    ?? ''
                                                }}

                                            </span>

                                        </div>

                                    @else

                                        <span class="text-secondary">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- ===============================
                                     Material
                                ================================ --}}
                                <td>

                                    @if ($transaction->reservation)

                                        <span>
                                            {{
                                                $transaction
                                                    ->reservation
                                                    ->material
                                                    ?->name
                                                ?? '-'
                                            }}
                                        </span>

                                    @else

                                        <span class="text-secondary">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- ===============================
                                     Points
                                ================================ --}}
                                <td class="text-center">

                                    @if ($transaction->point > 0)

                                        <span class="fw-bold text-success">
                                            +{{ number_format($transaction->point) }} pt
                                        </span>

                                    @elseif ($transaction->point < 0)

                                        <span class="fw-bold text-danger">
                                            {{ number_format($transaction->point) }} pt
                                        </span>

                                    @else

                                        <span class="fw-bold">
                                            0 pt
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-5 text-secondary"
                                >
                                    No point transactions yet.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection