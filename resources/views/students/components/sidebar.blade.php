<aside class="bg-light border-end min-vh-100 p-3">

    {{-- Back --}}
    <a
        href="{{ route('students.dashboard') }}"
        class="btn btn-link text-secondary p-0 mb-3 text-decoration-none"
    >
        ← Back to Dashboard
    </a>


    {{-- Title --}}
    <h5 class="fw-bold mb-4">
        Find a Teacher
    </h5>


    {{-- ===============================
         Keyword
    ================================ --}}
    <div class="mb-4">

        <label
            for="reservationKeyword"
            class="form-label fw-bold"
        >
            Keyword
        </label>

        <input
            type="text"
            id="reservationKeyword"
            class="form-control"
            placeholder="Name, career, specialty..."
        >

    </div>


    {{-- ===============================
         Favorite
    ================================ --}}
    <div class="mb-4">

        <div class="form-check">

            <input
                type="checkbox"
                class="form-check-input"
                id="reservationFavoriteOnly"
            >

            <label
                class="form-check-label fw-semibold"
                for="reservationFavoriteOnly"
            >
                ♥ Favorite Teachers
            </label>

        </div>

    </div>


    {{-- ===============================
         Material
    ================================ --}}
    <div class="mb-4">

        <label
            for="reservationMaterial"
            class="form-label fw-bold"
        >
            Material
        </label>

        <select
            id="reservationMaterial"
            class="form-select"
        >

            <option value="">
                All Materials
            </option>

            @foreach ($materials as $material)

                <option
                    value="{{ $material->material_id }}"
                >
                    {{ $material->name }}
                </option>

            @endforeach

        </select>

        <div class="form-text">
            Required before booking.
        </div>

    </div>


    {{-- ===============================
         Nationality
    ================================ --}}
    <div class="mb-4">

        <label
            for="reservationNationality"
            class="form-label fw-bold"
        >
            Nationality
        </label>

        <select
            id="reservationNationality"
            class="form-select"
        >

            <option value="">
                All
            </option>

            <option value="Philippines">
                Philippines
            </option>

            <option value="Japanese">
                Japan
            </option>

        </select>

    </div>


    {{-- ===============================
         Required Coins
    ================================ --}}
    <div class="mb-4">

        <label class="form-label fw-bold">
            Required Coins
        </label>


        <div
            id="pointRangeLabel"
            class="small text-secondary mb-3"
        >
            50 - 150
        </div>


        <input
            type="hidden"
            id="reservationMinPoints"
            value="50"
        >


        <input
            type="hidden"
            id="reservationMaxPoints"
            value="150"
        >


        <div
            id="pointRangeSlider"
            class="mx-2"
        >
        </div>

    </div>


    {{-- ===============================
         Rating
    ================================ --}}
    <div class="mb-4">

        <label class="form-label fw-bold d-block">
            Rating
        </label>


        <input
            type="hidden"
            id="reservationMinRating"
            value=""
        >


        <div
            id="ratingButtons"
            class="
                d-flex
                align-items-center
                gap-1
            "
        >

            @for ($rating = 1; $rating <= 5; $rating++)

                <button
                    type="button"
                    class="
                        btn
                        btn-link
                        p-0
                        text-secondary
                        text-decoration-none
                        rating-star
                    "
                    data-rating="{{ $rating }}"
                    style="
                        font-size: 24px;
                        line-height: 1;
                    "
                    aria-label="{{ $rating }} stars or more"
                >
                    ★
                </button>

            @endfor

        </div>


        <div class="small text-secondary mt-1">

            <span id="selectedRatingText">
                Any rating
            </span>

        </div>


        <button
            type="button"
            id="clearRatingBtn"
            class="
                btn
                btn-link
                btn-sm
                p-0
                mt-1
                text-decoration-none
            "
        >
            Clear
        </button>

    </div>


    <hr class="my-4">


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
            class="form-control"
            min="{{ now()->format('Y-m-d') }}"
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

            <div class="col-6">

                <input
                    type="number"
                    id="reservationHour"
                    class="form-control"
                    min="0"
                    max="23"
                    placeholder="Hour"
                >

            </div>


            <div class="col-6">

                <select
                    id="reservationMinute"
                    class="form-select"
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
