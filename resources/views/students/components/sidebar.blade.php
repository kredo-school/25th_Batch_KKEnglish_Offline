<aside class="bg-light border-end min-vh-100 p-3">

    <h5 class="fw-bold mb-4">
        Book a Lesson
    </h5>

    {{-- Date --}}
    <div class="mb-4">

        <label for="reservationDate"
               class="form-label fw-bold">

            Date

        </label>

        <input type="date"
               id="reservationDate"
               class="form-control">

    </div>


    {{-- Time --}}
    <div class="mb-4">

        <label for="reservationTime"
               class="form-label fw-bold">

            Time

        </label>

        <select id="reservationTime"
                class="form-select">

            <option value="">
                All Times
            </option>

            <option value="09:00">
                09:00
            </option>

            <option value="10:00">
                10:00
            </option>

            <option value="11:00">
                11:00
            </option>

            <option value="12:00">
                12:00
            </option>

            <option value="13:00">
                13:00
            </option>

            <option value="14:00">
                14:00
            </option>

            <option value="15:00">
                15:00
            </option>

            <option value="16:00">
                16:00
            </option>

            <option value="17:00">
                17:00
            </option>

        </select>

    </div>


    {{-- Material --}}
    <div class="mb-4">

        <label for="reservationMaterial"
               class="form-label fw-bold">

            Material

        </label>

        <select id="reservationMaterial"
                class="form-select">

            <option value="">
                All Materials
            </option>

            {{-- 仮データ --}}
            <option value="beginner">
                Beginner English
            </option>

            <option value="conversation">
                Daily Conversation
            </option>

            <option value="grammar">
                Grammar
            </option>

            <option value="business">
                Business English
            </option>

        </select>

    </div>

</aside>