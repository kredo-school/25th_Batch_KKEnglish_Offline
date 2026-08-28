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

                Please select a date, time, and material to book a lesson.

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
                     data-dates="2026-08-29,2026-08-30,2026-08-31"
                     data-times="09:00,10:00"
                     data-materials="beginner,conversation">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher1.jpg') }}"
                             alt="John"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body">

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

                            <a href="#"
                               class="btn btn-outline-primary btn-sm w-100 mb-2">

                                View Profile

                            </a>

                            <button type="button"
                                    class="btn btn-secondary btn-sm w-100 book-btn"
                                    disabled>

                                Book

                            </button>

                        </div>

                    </div>

                </div>


                {{-- Jane --}}
                <div class="col teacher-card"
                     data-dates="2026-08-30,2026-08-31"
                     data-times="10:00,11:00,12:00"
                     data-materials="grammar,business">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher2.jpg') }}"
                             alt="Jane"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body">

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

                            <a href="#"
                               class="btn btn-outline-primary btn-sm w-100 mb-2">

                                View Profile

                            </a>

                            <button type="button"
                                    class="btn btn-secondary btn-sm w-100 book-btn"
                                    disabled>

                                Book

                            </button>

                        </div>

                    </div>

                </div>


                {{-- Bob --}}
                <div class="col teacher-card"
                     data-dates="2026-08-29,2026-08-31"
                     data-times="09:00,11:00,13:00"
                     data-materials="beginner,grammar">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher3.jpg') }}"
                             alt="Bob"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body">

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

                            <a href="#"
                               class="btn btn-outline-primary btn-sm w-100 mb-2">

                                View Profile

                            </a>

                            <button type="button"
                                    class="btn btn-secondary btn-sm w-100 book-btn"
                                    disabled>

                                Book

                            </button>

                        </div>

                    </div>

                </div>


                {{-- Mary --}}
                <div class="col teacher-card"
                     data-dates="2026-08-30,2026-08-31"
                     data-times="10:00,12:00,14:00"
                     data-materials="conversation,business">

                    <div class="card h-100">

                        <img src="{{ asset('images/teacher4.jpg') }}"
                             alt="Mary"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body">

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

                            <a href="#"
                               class="btn btn-outline-primary btn-sm w-100 mb-2">

                                View Profile

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

    const dateInput =
        document.getElementById('reservationDate');

    const timeSelect =
        document.getElementById('reservationTime');

    const materialSelect =
        document.getElementById('reservationMaterial');

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


    function updateTeachers() {

        const date =
            dateInput.value;

        const time =
            timeSelect.value;

        const material =
            materialSelect.value;


        let visibleCount = 0;


        teacherCards.forEach(card => {

            const dates =
                card.dataset.dates.split(',');

            const times =
                card.dataset.times.split(',');

            const materials =
                card.dataset.materials.split(',');


            /*
             * 選択されていない条件は無視する
             */
            const matchDate =
                !date || dates.includes(date);

            const matchTime =
                !time || times.includes(time);

            const matchMaterial =
                !material || materials.includes(material);


            if (
                matchDate &&
                matchTime &&
                matchMaterial
            ) {

                card.classList.remove('d-none');

                visibleCount++;

            } else {

                card.classList.add('d-none');

            }

        });


        /*
         * Teacherが0件
         */
        if (visibleCount === 0) {

            noTeachers.classList.remove('d-none');

        } else {

            noTeachers.classList.add('d-none');

        }


        /*
         * 3条件すべて選択済みか
         */
        const allSelected =
            date &&
            time &&
            material;


        document.querySelectorAll('.book-btn')
            .forEach(button => {

                if (allSelected) {

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
         * メッセージ
         */
        if (allSelected) {

            selectionMessage.className =
                'alert alert-success';

            selectionMessage.textContent =
                'All selections are complete. Please choose a teacher.';

        } else {

            selectionMessage.className =
                'alert alert-light border';

            selectionMessage.textContent =
                'Please select a date, time, and material to book a lesson.';

        }


        /*
         * 選択条件のBadge
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


        // Date Badge
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


        // Time Badge
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


        // Material Badge
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
     * どれから選んでも自動Filter
     */
    dateInput.addEventListener(
        'change',
        updateTeachers
    );

    timeSelect.addEventListener(
        'change',
        updateTeachers
    );

    materialSelect.addEventListener(
        'change',
        updateTeachers
    );


    /*
     * 初期表示
     */
    updateTeachers();

</script>

@endsection