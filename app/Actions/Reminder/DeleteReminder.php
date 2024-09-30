<?php

namespace App\Actions\Appointment;
use App\Repositories\Contracts\AppointmentInterface;
use App\Enums\AppointmentEnum;
use Illuminate\Http\JsonResponse;

class DeleteReminder
{
    public function __construct(private AppointmentInterface $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function handle(string $id): JsonResponse
    {
        $now = now();
        $data = $this->appointmentRepository->delete($id);

        activity_log(AppointmentEnum::APPOINTMENT_DELETED, '', '/api/appointment/' . $id);

        return response()->json(
            [
                'status' => true,
                'message' => AppointmentEnum::APPOINTMENT_DELETED,
            ],
        );
    }
} 