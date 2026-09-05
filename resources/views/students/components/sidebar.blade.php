<aside class="bg-light border-end min-vh-100 p-3">

    <a
    href="{{ route('student.dashboard') }}"
    class="btn btn-link text-secondary p-0 mb-3 text-decoration-none">
    ← Back to Dashboard
    </a>

    {{-- ===============================
         Title
    ================================ --}}
    <h5 class="fw-bold mb-4">
        Book a Lesson
    </h5>


    {{-- ===============================
         Material
    ================================ --}}
    <div class="mb-4">

        <label
            for="reservationMaterial"
            class="form-label fw-bold"
        >
            Material

            <span class="text-danger">
                *
            </span>
        </label>


        <select
            id="reservationMaterial"
            name="material"
            class="form-select w-100"
        >

            {{-- 未選択 --}}
            <option value="">
                Select Material
            </option>


            {{-- ===============================
                 DBの教材一覧
            ================================ --}}
            @foreach ($materials as $material)

                <option
                    value="{{ $material->material_id }}"
                >

                    {{ $material->name }}

                </option>

            @endforeach

        </select>

    </div>


    {{-- ===============================
         Date
    ================================ --}}
    <div class="mb-4">

        <label
            for="reservationDate"
            class="form-label fw-bold"
        >
            Date
        </label>


        <input
            type="date"
            id="reservationDate"
            name="reservation_date"
            class="form-control w-100"
        >

    </div>


    {{-- ===============================
         Time
    ================================ --}}
    <div class="mb-4">

        <label class="form-label fw-bold">
            Time
        </label>


        <div class="row g-2">

            {{-- ===============================
                 Hour
            ================================ --}}
            <div class="col-6">

                <input
                    type="number"
                    id="reservationHour"
                    name="reservation_hour"
                    class="form-control w-100"
                    min="0"
                    max="23"
                    placeholder="Hour"
                >

            </div>


            {{-- ===============================
                 Minute
            ================================ --}}
            <div class="col-6">

                <select
                    id="reservationMinute"
                    name="reservation_minute"
                    class="form-select w-100"
                >

                    <option value="">
                        Minute
                    </option>


                    <option value="00">
                        00
                    </option>


                    <option value="30">
                        30
                    </option>

                </select>

            </div>

        </div>

    </div>

</aside>