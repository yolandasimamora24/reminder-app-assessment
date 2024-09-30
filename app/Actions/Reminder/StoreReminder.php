<?php

namespace App\Actions\Reminder;
use App\Repositories\Contracts\ReminderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreReminder
{
    public function __construct(private ReminderInterface $reminderRepository)
    {
        $this->reminderRepository = $reminderRepository;
    }

    public function handle(Request $request): JsonResponse
    {
        $now = now();
        $this->reminderRepository->create($request->all() + ['created_at' => $now, 'updated_at' => $now]);
        return response()->json(
            [
                'status' => true,
                'message' => 'Reminder Created',
            ],
        );
    }
} 