<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\TeacherSearchService;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TeacherSearchController extends Controller
{
    public function __construct(
        private TeacherSearchService $teacherSearchService,
        private AvailabilityService $availabilityService
    ) {}


    public function search(
        Request $request
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Login Student
        |--------------------------------------------------------------------------
        */

        $student =
            $request->user()->student;


        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([

                'keyword' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'material_id' => [
                    'nullable',
                    'integer',
                    'exists:materials,material_id',
                ],

                'nationality' => [
                    'nullable',
                    'string',
                    'in:Philippines,Japanese',
                ],

                'min_points' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'max_points' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'min_rating' => [
                    'nullable',
                    'numeric',
                    'between:1,5',
                ],

                'favorite_only' => [
                    'nullable',
                    'boolean',
                ],

                'date' => [
                    'nullable',
                    'date',
                    'after_or_equal:today',
                ],

                'time' => [
                    'nullable',
                    'date_format:H:i',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Point Range Validation
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $validated['min_points'],
                $validated['max_points']
            )
            &&
            $validated['min_points']
            >
            $validated['max_points']
        ) {

            throw ValidationException::withMessages([
                'min_points' =>
                    'Minimum points must not exceed maximum points.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | TimeにはDateが必要
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['time']
            )
            &&
            empty(
                $validated['date']
            )
        ) {

            throw ValidationException::withMessages([
                'date' =>
                    '時間を指定する場合は日付も選択してください。',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Teacher属性検索
        |--------------------------------------------------------------------------
        */

        $teachers =
            $this
                ->teacherSearchService
                ->search(
                    $validated,
                    $student->id
                );


        $results = [];


        /*
        |--------------------------------------------------------------------------
        | Teacherごとに確認
        |--------------------------------------------------------------------------
        */

        foreach (
            $teachers as $teacher
        ) {

            /*
             * 基本Teacher情報
             */
            $teacherData = [

                'id' =>
                    $teacher->id,

                'first_name' =>
                    $teacher->user?->first_name,

                'last_name' =>
                    $teacher->user?->last_name,

                'profile_image' =>
                    $teacher->user?->profile_image,

                'nationality' =>
                    $teacher->user?->nationality,

                'career' =>
                    $teacher->career,

                'specialty' =>
                    $teacher->specialty,

                'certification' =>
                    $teacher->certification,

                'graduation_school' =>
                    $teacher->graduation_school,

                'about_me' =>
                    $teacher->about_me,

                'point_consumed' =>
                    $teacher->point_consumed,

                'reviews_avg_rating' =>
                    $teacher->reviews_avg_rating,

                'reviews_count' =>
                    $teacher->reviews_count,

                'materials' =>
                    $teacher
                        ->materials
                        ->map(
                            function ($material) {

                                return [
                                    'material_id' =>
                                        $material->material_id,

                                    'name' =>
                                        $material->name,
                                ];
                            }
                        )
                        ->values(),

                /*
                 * Date未指定時は
                 * Availability未確認
                 */
                'availability_checked' =>
                    false,

                'available' =>
                    null,

                'schedule_id' =>
                    null,

                'start_at' =>
                    null,

                'end_at' =>
                    null,
            ];


            /*
            |--------------------------------------------------------------------------
            | Date未指定
            |--------------------------------------------------------------------------
            */

            if (
                empty(
                    $validated['date']
                )
            ) {

                $results[] =
                    $teacherData;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | AvailabilityService
            |--------------------------------------------------------------------------
            */

            $availability =
                $this
                    ->availabilityService
                    ->getAvailability(
                        $teacher->id,
                        $validated['date'],
                        $student->id
                    );


            $slots =
                $availability;


            /*
            |--------------------------------------------------------------------------
            | Date + Time
            |--------------------------------------------------------------------------
            */

            if (
                !empty(
                    $validated['time']
                )
            ) {

                $matchedSlot =
                    null;


                foreach (
                    $slots as $slot
                ) {

                    /*
                     * 予約不可Slotは除外
                     */
                    if (
                        ($slot['available'] ?? false)
                        !== true
                    ) {

                        continue;
                    }


                    if (
                        empty(
                            $slot['start_at']
                        )
                    ) {

                        continue;
                    }


                    $slotTime =
                        Carbon::parse(
                            $slot['start_at']
                        )->format(
                            'H:i'
                        );


                    if (
                        $slotTime
                        ===
                        $validated['time']
                    ) {

                        $matchedSlot =
                            $slot;

                        break;
                    }
                }


                /*
                 * 指定時間に空きなし
                 */
                if (!$matchedSlot) {

                    continue;
                }


                /*
                 * 空きあり
                 */
                $teacherData[
                    'availability_checked'
                ] = true;


                $teacherData[
                    'available'
                ] = true;


                $teacherData[
                    'schedule_id'
                ] =
                    $matchedSlot['schedule_id']
                    ?? null;


                $teacherData[
                    'start_at'
                ] =
                    $matchedSlot['start_at']
                    ?? null;


                $teacherData[
                    'end_at'
                ] =
                    $matchedSlot['end_at']
                    ?? null;


                $results[] =
                    $teacherData;


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Date Only
            |--------------------------------------------------------------------------
            |
            | その日に1枠でも空きがあれば表示
            |--------------------------------------------------------------------------
            */

            $hasAvailableSlot =
                false;


            foreach (
                $slots as $slot
            ) {

                if (
                    ($slot['available'] ?? false)
                    === true
                ) {

                    $hasAvailableSlot =
                        true;

                    break;
                }
            }


            /*
             * 1枠も空いていない
             */
            if (!$hasAvailableSlot) {

                continue;
            }


            $teacherData[
                'availability_checked'
            ] = true;


            $teacherData[
                'available'
            ] = true;


            $results[] =
                $teacherData;
        }


        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'teachers' =>
                $results,

            'count' =>
                count(
                    $results
                ),
        ]);
    }
}
