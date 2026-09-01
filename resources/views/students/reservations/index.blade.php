@extends('layouts.app')

@section('title', 'Book a Lesson')

@section('content')

<div class="container-fluid">

    <div class="row">

        {{-- ===============================
             Main Content
        ================================ --}}
        <main class="col-md-9 col-lg-10 px-4 py-4">

            <h2 class="fw-bold mb-3">
                Available Teachers
            </h2>


            {{-- 選択状況 --}}
            <div id="selectionMessage"
                 class="alert alert-light border">

                Please select a date and material.

            </div>


            {{-- 選択条件表示 --}}
            <div id="selectedConditions"
                 class="mb-4 d-none">

                <span class="text-secondary me-2">
                    Selected:
                </span>

                <span id="selectedDate"
                      class="badge text-bg-light border me-1 d-none">
                </span>

                <span id="selectedTime"
                      class="badge text-bg-light border me-1 d-none">
                </span>

                <span id="selectedMaterial"
                      class="badge text-bg-light border me-1 d-none">
                </span>

            </div>


            {{-- ===============================
                 Teacher List
            ================================ --}}
            <div id="teacherList"
                 class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">


                {{-- John --}}
                <div class="col teacher-card"
                     data-dates="2026-09-01,2026-09-02,2026-09-03"
                     data-times="09:00,09:30,10:00,10:30"
                     data-materials="beginner,conversation">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher1.jpg') }}"
                             alt="John"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold mb-2">
                                John
                            </h5>

                            <p class="mb-1 small">
                                <span class="text-secondary">
                                    Nationality:
                                </span>
                                Philippines
                            </p>

                            <p class="mb-3 small">
                                <span class="text-secondary">
                                    Specialty:
                                </span>
                                Daily Conversation
                            </p>


                            {{-- ボタン位置をカード下部に揃える --}}
                            <div class="mt-auto">

                                <a href="#"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2
                                          view-schedule-btn disabled"
                                   aria-disabled="true">

                                    View Schedule

                                </a>

                                <button type="button"
                                        class="btn btn-secondary btn-sm w-100 book-btn"
                                        disabled>

                                    Book

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Jane --}}
                <div class="col teacher-card"
                     data-dates="2026-09-01,2026-09-02"
                     data-times="10:00,10:30,11:00,11:30,12:00"
                     data-materials="grammar,business">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher2.jpg') }}"
                             alt="Jane"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold mb-2">
                                Jane
                            </h5>

                            <p class="mb-1 small">
                                <span class="text-secondary">
                                    Nationality:
                                </span>
                                Philippines
                            </p>

                            <p class="mb-3 small">
                                <span class="text-secondary">
                                    Specialty:
                                </span>
                                Grammar
                            </p>

                            <div class="mt-auto">

                                <a href="#"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2
                                          view-schedule-btn disabled"
                                   aria-disabled="true">

                                    View Schedule

                                </a>

                                <button type="button"
                                        class="btn btn-secondary btn-sm w-100 book-btn"
                                        disabled>

                                    Book

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Bob --}}
                <div class="col teacher-card"
                     data-dates="2026-09-01,2026-09-03"
                     data-times="09:00,09:30,11:00,11:30,13:00"
                     data-materials="beginner,grammar">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher3.jpg') }}"
                             alt="Bob"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold mb-2">
                                Bob
                            </h5>

                            <p class="mb-1 small">
                                <span class="text-secondary">
                                    Nationality:
                                </span>
                                Philippines
                            </p>

                            <p class="mb-3 small">
                                <span class="text-secondary">
                                    Specialty:
                                </span>
                                Pronunciation
                            </p>

                            <div class="mt-auto">

                                <a href="#"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2
                                          view-schedule-btn disabled"
                                   aria-disabled="true">

                                    View Schedule

                                </a>

                                <button type="button"
                                        class="btn btn-secondary btn-sm w-100 book-btn"
                                        disabled>

                                    Book

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Mary --}}
                <div class="col teacher-card"
                     data-dates="2026-09-02,2026-09-03"
                     data-times="10:00,10:30,12:00,12:30,14:00"
                     data-materials="conversation,business">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher4.jpg') }}"
                             alt="Mary"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold mb-2">
                                Mary
                            </h5>

                            <p class="mb-1 small">
                                <span class="text-secondary">
                                    Nationality:
                                </span>
                                Philippines
                            </p>

                            <p class="mb-3 small">
                                <span class="text-secondary">
                                    Specialty:
                                </span>
                                Business English
                            </p>

                            <div class="mt-auto">

                                <a href="#"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2
                                          view-schedule-btn disabled"
                                   aria-disabled="true">

                                    View Schedule

                                </a>

                                <button type="button"
                                        class="btn btn-secondary btn-sm w-100 book-btn"
                                        disabled>

                                    Book

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Teacher 0件 --}}
            <div id="noTeachers"
                 class="text-center py-5 d-none">

                <h5 class="fw-bold">
                    No teachers available
                </h5>

                <p class="text-secondary">
                    Please change your search conditions.
                </p>

            </div>

        </main>

    </div>

</div>


<script>

    /*
     * ===============================
     * Sidebar inputs
     * ===============================
     */

    const dateInput =
        document.getElementById('reservationDate');

    const hourInput =
        document.getElementById('reservationHour');

    const minuteSelect =
        document.getElementById('reservationMinute');

    const materialSelect =
        document.getElementById('reservationMaterial');


    /*
     * ===============================
     * Main elements
     * ===============================
     */

    const teacherCards =
        document.querySelectorAll('.teacher-card');

    const noTeachers =
        document.getElementById('noTeachers');

    const selectionMessage =
        document.getElementById('selectionMessage');

    const selectedConditions =
        document.getElementById('selectedConditions');

    const selectedDate =
        document.getElementById('selectedDate');

    const selectedTime =
        document.getElementById('selectedTime');

    const selectedMaterial =
        document.getElementById('selectedMaterial');


    /*
     * ===============================
     * Teacher Filter
     * ===============================
     */

    function updateTeachers() {

        const date =
            dateInput.value;

        const hour =
            hourInput.value;

        const minute =
            minuteSelect.value;

        const material =
            materialSelect.value;


        /*
         * Hour + Minute が両方選択された場合だけ
         * 09:00 のような形式にする
         */
        const time =
            hour && minute
                ? String(hour).padStart(2, '0') + ':' + minute
                : '';


        /*
         * 2条件
         * Date + Material
         *
         * → View Schedule 有効
         */
        const twoSelected =
            date &&
            material;


        /*
         * 3条件
         * Date + Material + Time
         *
         * → Book 有効
         */
        const threeSelected =
            date &&
            material &&
            time;


        let visibleCount = 0;


        /*
         * ===============================
         * Teacher Card Filter
         * ===============================
         */

        teacherCards.forEach(card => {

            const dates =
                card.dataset.dates.split(',');

            const times =
                card.dataset.times.split(',');

            const materials =
                card.dataset.materials.split(',');


            /*
             * 未選択の条件は無視
             */
            const matchDate =
                !date || dates.includes(date);

            const matchMaterial =
                !material || materials.includes(material);

            const matchTime =
                !time || times.includes(time);


            if (
                matchDate &&
                matchMaterial &&
                matchTime
            ) {

                card.classList.remove('d-none');

                visibleCount++;

            } else {

                card.classList.add('d-none');

            }

        });


        /*
         * ===============================
         * Teacherが0件
         * ===============================
         */

        if (visibleCount === 0) {

            noTeachers.classList.remove('d-none');

        } else {

            noTeachers.classList.add('d-none');

        }


        /*
         * ===============================
         * View Schedule
         *
         * Date + Materialで有効
         * ===============================
         */

        document.querySelectorAll('.view-schedule-btn')
            .forEach(button => {

                if (twoSelected) {

                    button.classList.remove('disabled');

                    button.removeAttribute(
                        'aria-disabled'
                    );

                } else {

                    button.classList.add('disabled');

                    button.setAttribute(
                        'aria-disabled',
                        'true'
                    );

                }

            });


        /*
         * ===============================
         * Book
         *
         * Date + Material + Timeで有効
         * ===============================
         */

        document.querySelectorAll('.book-btn')
            .forEach(button => {

                if (threeSelected) {

                    button.disabled = false;

                    button.classList.remove(
                        'btn-secondary'
                    );

                    button.classList.add(
                        'btn-primary'
                    );

                } else {

                    button.disabled = true;

                    button.classList.remove(
                        'btn-primary'
                    );

                    button.classList.add(
                        'btn-secondary'
                    );

                }

            });


        /*
         * ===============================
         * Message
         * ===============================
         */

        if (threeSelected) {

            selectionMessage.className =
                'alert alert-success';

            selectionMessage.textContent =
                'All selections are complete. Please choose a teacher to book.';

        } else if (twoSelected) {

            selectionMessage.className =
                'alert alert-info';

            selectionMessage.textContent =
                'You can view a teacher schedule, or select a time to book directly.';

        } else {

            selectionMessage.className =
                'alert alert-light border';

            selectionMessage.textContent =
                'Please select a date and material.';

        }


        /*
         * ===============================
         * Selected Conditions
         * ===============================
         */

        const hasCondition =
            date ||
            time ||
            material;


        if (hasCondition) {

            selectedConditions.classList.remove(
                'd-none'
            );

        } else {

            selectedConditions.classList.add(
                'd-none'
            );

        }


        /*
         * Date Badge
         */

        if (date) {

            selectedDate.textContent =
                date;

            selectedDate.classList.remove(
                'd-none'
            );

        } else {

            selectedDate.classList.add(
                'd-none'
            );

        }


        /*
         * Time Badge
         */

        if (time) {

            selectedTime.textContent =
                time;

            selectedTime.classList.remove(
                'd-none'
            );

        } else {

            selectedTime.classList.add(
                'd-none'
            );

        }


        /*
         * Material Badge
         */

        if (material) {

            selectedMaterial.textContent =
                materialSelect
                    .options[
                        materialSelect.selectedIndex
                    ]
                    .text;

            selectedMaterial.classList.remove(
                'd-none'
            );

        } else {

            selectedMaterial.classList.add(
                'd-none'
            );

        }

    }


    /*
     * ===============================
     * Event
     * ===============================
     */

    dateInput.addEventListener(
        'change',
        updateTeachers
    );

    hourInput.addEventListener(
        'input',
        updateTeachers
    );

    minuteSelect.addEventListener(
        'change',
        updateTeachers
    );

    materialSelect.addEventListener(
        'change',
        updateTeachers
    );


    /*
     * ===============================
     * Initial Display
     * ===============================
     */

    updateTeachers();

</script>

@endsection