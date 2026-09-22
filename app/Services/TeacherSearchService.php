<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

class TeacherSearchService
{
    public function search(
        array $conditions = []
    ): Collection {

        $query = Teacher::query()
            ->with([
                'user',
                'materials',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');


        /*
         * Material
         */
        if (!empty($conditions['material_id'])) {

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
         * Nationality
         */
        if (!empty($conditions['nationality'])) {

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
         * Maximum Points
         */
        if (!empty($conditions['max_points'])) {

            $query->where(
                'point_consumed',
                '<=',
                $conditions['max_points']
            );
        }


        /*
         * Minimum Rating
         */
        if (!empty($conditions['min_rating'])) {

            $query->having(
                'reviews_avg_rating',
                '>=',
                $conditions['min_rating']
            );
        }

        /*
         * Keyword Search
        */
        if (!empty($conditions['keyword'])) {

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
        return $query->get();
    }
}
