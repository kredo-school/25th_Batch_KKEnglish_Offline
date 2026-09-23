<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherLike;
use Illuminate\Database\Eloquent\Collection;

class TeacherSearchService
{
    public function search(
        array $conditions = [],
        ?int $studentId = null
    ): Collection {

        /*
        |--------------------------------------------------------------------------
        | 基本Query
        |--------------------------------------------------------------------------
        */

        $query = Teacher::query()
            ->with([
                'user',
                'materials',
            ])
            ->withCount(
                'reviews'
            )
            ->withAvg(
                'reviews',
                'rating'
            );


        /*
        |--------------------------------------------------------------------------
        | Material
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $conditions['material_id']
            )
        ) {

            $query->whereHas(
                'materials',
                function ($query) use ($conditions) {

                    $query->where(
                        'materials.material_id',
                        $conditions['material_id']
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Nationality
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $conditions['nationality']
            )
        ) {

            $query->whereHas(
                'user',
                function ($query) use ($conditions) {

                    $query->where(
                        'nationality',
                        $conditions['nationality']
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Minimum Points
        |--------------------------------------------------------------------------
        |
        | 0も有効な値なので
        | empty()ではなくarray_key_exists()
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'min_points',
                $conditions
            )
            &&
            $conditions['min_points'] !== null
            &&
            $conditions['min_points'] !== ''
        ) {

            $query->where(
                'point_consumed',
                '>=',
                $conditions['min_points']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Maximum Points
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'max_points',
                $conditions
            )
            &&
            $conditions['max_points'] !== null
            &&
            $conditions['max_points'] !== ''
        ) {

            $query->where(
                'point_consumed',
                '<=',
                $conditions['max_points']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Minimum Rating
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $conditions['min_rating']
            )
        ) {

            $query->having(
                'reviews_avg_rating',
                '>=',
                $conditions['min_rating']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Favorite Teachers Only
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $conditions['favorite_only']
            )
            &&
            $studentId
        ) {

            $query->whereIn(
                'id',
                TeacherLike::query()
                    ->where(
                        'student_id',
                        $studentId
                    )
                    ->select(
                        'teacher_id'
                    )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Keyword
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $conditions['keyword']
            )
        ) {

            $keyword =
                $conditions['keyword'];


            $query->where(
                function ($query) use ($keyword) {

                    $query
                        ->where(
                            'career',
                            'like',
                            '%' . $keyword . '%'
                        )

                        ->orWhere(
                            'specialty',
                            'like',
                            '%' . $keyword . '%'
                        )

                        ->orWhere(
                            'certification',
                            'like',
                            '%' . $keyword . '%'
                        )

                        ->orWhere(
                            'graduation_school',
                            'like',
                            '%' . $keyword . '%'
                        )

                        ->orWhere(
                            'about_me',
                            'like',
                            '%' . $keyword . '%'
                        )

                        ->orWhereHas(
                            'user',
                            function ($userQuery) use ($keyword) {

                                $userQuery
                                    ->where(
                                        'first_name',
                                        'like',
                                        '%' . $keyword . '%'
                                    )

                                    ->orWhere(
                                        'last_name',
                                        'like',
                                        '%' . $keyword . '%'
                                    )

                                    ->orWhere(
                                        'nationality',
                                        'like',
                                        '%' . $keyword . '%'
                                    );
                            }
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 検索実行
        |--------------------------------------------------------------------------
        */

        return $query->get();
    }
}
