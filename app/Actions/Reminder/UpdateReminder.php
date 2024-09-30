<?php

namespace App\Actions\Appointment;
use App\Repositories\Contracts\AppointmentInterface;
use App\Enums\AppointmentEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateReminder
{
    public function __construct(private AppointmentInterface $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function handle(Request $request, string $id): JsonResponse
    {
        $now = now();
        $this->appointmentRepository->update($id, $request->validated() + ['updated_at' => now()]);

        activity_log(AppointmentEnum::APPOINTMENT_UPDATED, '', '/api/appointment/' . $id);

        return response()->json(
            [
                'status' => true,
                'message' => AppointmentEnum::APPOINTMENT_UPDATED,
            ],
        );
    }
} 