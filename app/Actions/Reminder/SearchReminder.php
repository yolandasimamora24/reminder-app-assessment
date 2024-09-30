<?php

namespace App\Actions\Appointment;
use App\Repositories\Contracts\AppointmentInterface;
use App\Enums\AppointmentEnum;
use App\Services\CommonResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchReminder
{
    public function __construct(private AppointmentInterface $appointmentRepository, private CommonResponseService $response, private int $perPage = 20)
    {
        $this->response = $response;
        $this->appointmentRepository = $appointmentRepository;
        $this->perPage = request('per_page', 20);
    }

    public function handle(Request $request): JsonResponse
    {
        $data = $this->appointmentRepository->findAll(
            columns: ['user_id', 'email', 'status', 'reminder_date', 'type'],
            perPage: $this->perPage,
            params: $request->validated(),
        );
        return $this->response->paginatedResponse(
            data: $data,
        );   
    }
} 