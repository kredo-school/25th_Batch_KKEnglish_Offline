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


            {{-- ===============================
                 選択状況メッセージ
            ================================ --}}
            <div
                id="selectionMessage"
                class="alert alert-light border"
            >
                Please select a material.
            </div>


            {{-- ===============================
                 選択条件表示
            ================================ --}}
            <div
                id="selectedConditions"
                class="mb-4 d-none"
            >

                <span class="text-secondary me-2">
                    Selected:
                </span>


                <span
                    id="selectedDate"
                    class="
                        badge
                        text-bg-light
                        border
                        me-1
                        d-none
                    "
                >
                </span>


                <span
                    id="selectedTime"
                    class="
                        badge
                        text-bg-light
                        border
                        me-1
                        d-none
                    "
                >
                </span>


                <span
                    id="selectedMaterial"
                    class="
                        badge
                        text-bg-light
                        border
                        me-1
                        d-none
                    "
                >
                </span>

            </div>


            {{-- ===============================
                 Teacher List
            ================================ --}}
            <div
                id="teacherList"
                class="
                    row
                    row-cols-1
                    row-cols-md-2
                    row-cols-lg-4
                    g-4
                "
            >
            </div>


            {{-- ===============================
                 Teacher 0件
            ================================ --}}
            <div
                id="noTeachers"
                class="text-center py-5 d-none"
            >

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
    |--------------------------------------------------------------------------
    | Sidebar Inputs
    |--------------------------------------------------------------------------
    */

    const dateInput =
        document.getElementById(
            'reservationDate'
        );


    const hourInput =
        document.getElementById(
            'reservationHour'
        );


    const minuteSelect =
        document.getElementById(
            'reservationMinute'
        );


    const materialSelect =
        document.getElementById(
            'reservationMaterial'
        );



    /*
    |--------------------------------------------------------------------------
    | Main Elements
    |--------------------------------------------------------------------------
    */

    const teacherList =
        document.getElementById(
            'teacherList'
        );


    const noTeachers =
        document.getElementById(
            'noTeachers'
        );


    const selectionMessage =
        document.getElementById(
            'selectionMessage'
        );


    const selectedConditions =
        document.getElementById(
            'selectedConditions'
        );


    const selectedDate =
        document.getElementById(
            'selectedDate'
        );


    const selectedTime =
        document.getElementById(
            'selectedTime'
        );


    const selectedMaterial =
        document.getElementById(
            'selectedMaterial'
        );



    /*
    |--------------------------------------------------------------------------
    | Mock JSON Data
    |--------------------------------------------------------------------------
    |
    | 現在はController/API未接続なので
    | フロント確認用の仮データ。
    |
    | available_slots
    |
    | 日付 + 時間を1つの予約枠として持たせる。
    |
    */

    const mockTeachers = [

        {
            teacher_id: 1,

            first_name: 'John',
            last_name: 'Smith',

            nationality: 'Philippines',

            specialty:
                'Daily Conversation',

            profile_image:
                '/images/teacher1.jpg',


            materials: [
                'beginner',
                'conversation'
            ],


            available_slots: [

                {
                    date: '2026-09-03',
                    time: '09:00'
                },

                {
                    date: '2026-09-03',
                    time: '09:30'
                },

                {
                    date: '2026-09-03',
                    time: '10:30'
                },

                {
                    date: '2026-09-04',
                    time: '09:00'
                },

                {
                    date: '2026-09-04',
                    time: '10:00'
                },

                {
                    date: '2026-09-05',
                    time: '11:00'
                }

            ]
        },


        {
            teacher_id: 2,

            first_name: 'Jane',
            last_name: 'Doe',

            nationality: 'Philippines',

            specialty:
                'Grammar',

            profile_image:
                '/images/teacher2.jpg',


            materials: [
                'grammar',
                'business'
            ],


            available_slots: [

                {
                    date: '2026-09-03',
                    time: '10:00'
                },

                {
                    date: '2026-09-03',
                    time: '10:30'
                },

                {
                    date: '2026-09-04',
                    time: '11:00'
                },

                {
                    date: '2026-09-04',
                    time: '11:30'
                },

                {
                    date: '2026-09-05',
                    time: '12:00'
                }

            ]
        },


        {
            teacher_id: 3,

            first_name: 'Bob',
            last_name: 'Lee',

            nationality: 'Philippines',

            specialty:
                'Pronunciation',

            profile_image:
                '/images/teacher3.jpg',


            materials: [
                'beginner',
                'grammar'
            ],


            available_slots: [

                {
                    date: '2026-09-03',
                    time: '11:00'
                },

                {
                    date: '2026-09-03',
                    time: '11:30'
                },

                {
                    date: '2026-09-05',
                    time: '09:00'
                },

                {
                    date: '2026-09-05',
                    time: '09:30'
                },

                {
                    date: '2026-09-05',
                    time: '13:00'
                }

            ]
        },


        {
            teacher_id: 4,

            first_name: 'Mary',
            last_name: 'Jones',

            nationality: 'Philippines',

            specialty:
                'Business English',

            profile_image:
                '/images/teacher4.jpg',


            materials: [
                'conversation',
                'business'
            ],


            available_slots: [

                {
                    date: '2026-09-04',
                    time: '10:00'
                },

                {
                    date: '2026-09-04',
                    time: '10:30'
                },

                {
                    date: '2026-09-05',
                    time: '12:00'
                },

                {
                    date: '2026-09-05',
                    time: '12:30'
                },

                {
                    date: '2026-09-05',
                    time: '14:00'
                }

            ]
        }

    ];



    /*
    |--------------------------------------------------------------------------
    | Teacher Card Render
    |--------------------------------------------------------------------------
    */

    function renderTeachers(
        teachers,
        date,
        time,
        material
    ) {

        teacherList.innerHTML = '';


        /*
         * 0件
         */
        if (teachers.length === 0) {

            noTeachers.classList.remove(
                'd-none'
            );

            return;
        }


        /*
         * 先生あり
         */
        noTeachers.classList.add(
            'd-none'
        );


        teachers.forEach(
            function (teacher) {


                const col =
                    document.createElement(
                        'div'
                    );


                col.className =
                    'col';



                /*
                 * ===============================
                 * View Schedule URL
                 * ===============================
                 */

                const params =
                    new URLSearchParams();


                /*
                 * 教材
                 */
                if (material) {

                    params.append(
                        'material',
                        material
                    );

                }


                /*
                 * 日付
                 */
                if (date) {

                    params.append(
                        'date',
                        date
                    );

                }


                /*
                 * テスト用
                 *
                 * 日付がある
                 * → mode=date
                 *
                 * 日付なし
                 * → mode=material
                 */
                if (date) {

                    params.append(
                        'mode',
                        'date'
                    );

                } else {

                    params.append(
                        'mode',
                        'material'
                    );

                }


                const scheduleUrl =
                    "{{ route('reservations.teacher-detail.test') }}"
                    + '?'
                    + params.toString();



                /*
                 * ===============================
                 * Direct Book
                 * ===============================
                 *
                 * 教材 + 日付 + 時間
                 * が全部選択されている場合のみ
                 *
                 */

                const canDirectBook =
                    Boolean(
                        material &&
                        date &&
                        time
                    );



                /*
                 * ===============================
                 * Card
                 * ===============================
                 */

                col.innerHTML = `

                    <div class="card h-100">

                        <img
                            src="${teacher.profile_image}"
                            alt="${teacher.first_name}"
                            class="card-img-top"
                            style="
                                height: 180px;
                                object-fit: cover;
                            "
                        >


                        <div class="
                            card-body
                            d-flex
                            flex-column
                        ">


                            <h5 class="fw-bold mb-2">

                                ${teacher.first_name}

                                ${teacher.last_name ?? ''}

                            </h5>


                            <p class="mb-1 small">

                                <span class="text-secondary">

                                    Nationality:

                                </span>

                                ${teacher.nationality ?? ''}

                            </p>


                            <p class="mb-3 small">

                                <span class="text-secondary">

                                    Specialty:

                                </span>

                                ${teacher.specialty ?? ''}

                            </p>



                            <div class="mt-auto">


                                <a
                                    href="${scheduleUrl}"
                                    class="
                                        btn
                                        btn-outline-primary
                                        btn-sm
                                        w-100
                                        mb-2
                                    "
                                >

                                    View Schedule

                                </a>



                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-sm
                                        w-100

                                        ${
                                            canDirectBook
                                                ? 'btn-primary'
                                                : 'btn-secondary'
                                        }
                                    "
                                    data-teacher-id="${teacher.teacher_id}"

                                    ${
                                        canDirectBook
                                            ? ''
                                            : 'disabled'
                                    }
                                >

                                    Book

                                </button>


                            </div>

                        </div>

                    </div>

                `;


                teacherList.appendChild(
                    col
                );

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Update Teachers
    |--------------------------------------------------------------------------
    */

    function updateTeachers() {


        /*
         * ===============================
         * 選択値取得
         * ===============================
         */

        const date =
            dateInput.value;


        const hour =
            hourInput.value;


        const minute =
            minuteSelect.value;


        const material =
            materialSelect.value;



        /*
         * ===============================
         * 時間を作る
         * ===============================
         *
         * 09 + 30
         * ↓
         * 09:30
         *
         */

        const time =
            hour &&
            minute

                ? String(hour)
                    .padStart(
                        2,
                        '0'
                    )
                    + ':'
                    + minute

                : '';



        /*
         * ===============================
         * View Schedule
         * ===============================
         *
         * 教材を選択していれば
         * View Schedule可能
         *
         */

        const canViewSchedule =
            Boolean(
                material
            );



        /*
         * ===============================
         * Direct Book
         * ===============================
         *
         * 教材 + 日付 + 時間
         *
         */

        const canDirectBook =
            Boolean(
                material &&
                date &&
                time
            );



        /*
         * ===============================
         * Teacher Filter
         * ===============================
         */

        const filteredTeachers =
            mockTeachers.filter(
                function (teacher) {


                    /*
                     * ===============================
                     * 教材
                     * ===============================
                     */

                    const matchMaterial =
                        !material ||
                        teacher
                            .materials
                            .includes(
                                material
                            );



                    /*
                     * ===============================
                     * 日付
                     * ===============================
                     *
                     * 指定した日に
                     * 1枠でも空きがあるか
                     *
                     */

                    const matchDate =
                        !date ||
                        teacher
                            .available_slots
                            .some(
                                function (slot) {

                                    return (
                                        slot.date
                                        === date
                                    );

                                }
                            );



                    /*
                     * ===============================
                     * 日付 + 時間
                     * ===============================
                     *
                     * 日付と時間が
                     * 同じslotに存在するか
                     *
                     */

                    const matchDateTime =
                        !date ||
                        !time ||
                        teacher
                            .available_slots
                            .some(
                                function (slot) {

                                    return (

                                        slot.date
                                            === date

                                        &&

                                        slot.time
                                            === time

                                    );

                                }
                            );



                    return (

                        matchMaterial

                        &&

                        matchDate

                        &&

                        matchDateTime

                    );

                }
            );



        /*
         * ===============================
         * 先生表示
         * ===============================
         *
         * 教材未選択
         * → まだ先生は出さない
         *
         */

        if (!material) {

            teacherList.innerHTML =
                '';


            noTeachers.classList.add(
                'd-none'
            );


        } else {

            renderTeachers(
                filteredTeachers,
                date,
                time,
                material
            );

        }



        /*
         * ===============================
         * Message
         * ===============================
         */


        /*
         * 教材未選択
         */
        if (!material) {

            selectionMessage.className =
                'alert alert-light border';


            selectionMessage.textContent =
                'Please select a material.';

        }


        /*
         * 先生0件
         */
        else if (
            filteredTeachers.length
            === 0
        ) {

            selectionMessage.className =
                'alert alert-warning';


            selectionMessage.textContent =
                'No teachers are available for the selected conditions.';

        }


        /*
         * 3条件選択 + 先生あり
         */
        else if (
            canDirectBook
        ) {

            selectionMessage.className =
                'alert alert-success';


            selectionMessage.textContent =
                'You can book directly or view the teacher schedule.';

        }


        /*
         * 教材 + 日付
         */
        else if (
            material &&
            date
        ) {

            selectionMessage.className =
                'alert alert-info';


            selectionMessage.textContent =
                'You can view the weekly schedule. The selected date will be highlighted.';

        }


        /*
         * 教材のみ
         */
        else if (
            canViewSchedule
        ) {

            selectionMessage.className =
                'alert alert-info';


            selectionMessage.textContent =
                'You can view each teacher’s weekly schedule.';

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

            selectedConditions
                .classList
                .remove(
                    'd-none'
                );

        } else {

            selectedConditions
                .classList
                .add(
                    'd-none'
                );

        }



        /*
         * ===============================
         * Date Badge
         * ===============================
         */

        if (date) {

            selectedDate.textContent =
                date;


            selectedDate
                .classList
                .remove(
                    'd-none'
                );

        } else {

            selectedDate
                .classList
                .add(
                    'd-none'
                );

        }



        /*
         * ===============================
         * Time Badge
         * ===============================
         */

        if (time) {

            selectedTime.textContent =
                time;


            selectedTime
                .classList
                .remove(
                    'd-none'
                );

        } else {

            selectedTime
                .classList
                .add(
                    'd-none'
                );

        }



        /*
         * ===============================
         * Material Badge
         * ===============================
         */

        if (material) {

            selectedMaterial.textContent =

                materialSelect
                    .options[
                        materialSelect
                            .selectedIndex
                    ]
                    .text;


            selectedMaterial
                .classList
                .remove(
                    'd-none'
                );

        } else {

            selectedMaterial
                .classList
                .add(
                    'd-none'
                );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | Event
    |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | Initial Display
    |--------------------------------------------------------------------------
    */

    updateTeachers();



    /*
    |--------------------------------------------------------------------------
    | TODO: API接続
    |--------------------------------------------------------------------------
    |
    | 最終的には
    |
    | mockTeachers
    |
    | を削除して、
    |
    | Controller
    | ↓
    | JSON
    | ↓
    | JavaScript
    |
    | に置き換える。
    |
    |
    | JSON例:
    |
    | {
    |     teacher_id: 1,
    |
    |     available_slots: [
    |
    |         {
    |             date: '2026-09-03',
    |             time: '09:00'
    |         }
    |
    |     ]
    | }
    |
    */

</script>

@endsection
