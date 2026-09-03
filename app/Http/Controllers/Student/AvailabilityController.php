<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],
            'date' => [
                'required',
                'date_format:Y-m-d',
            ],
        ]);

        $slots = $this->service->getAvailability(
            (int) $validated['teacher_id'],
            $validated['date']
        );

        return response()->json([
            'teacher_id' => (int) $validated['teacher_id'],
            'date' => $validated['date'],
            'slots' => $slots,
        ]);
    }
}
