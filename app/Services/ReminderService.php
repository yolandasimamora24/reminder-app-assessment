<?php

namespace App\Services;

use Exception;
use App\Http\Requests\Reminder\ReminderCreateRequest;
use App\Http\Requests\Reminder\ReminderIndexRequest;
use App\Http\Requests\Reminder\ReminderUpdateRequest;
use App\Repositories\Contracts\ReminderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReminderService
{
    public function __construct(private ReminderInterface $reminderRepository, private CommonResponseService $response,  private int $perPage = 20)
    {
        $this->response = $response;
        $this->reminderRepository = $reminderRepository;
        $this->perPage = request('per_page', 20);
    }

    /**
     * Save a new record
     *
     */
    public function save(ReminderCreateRequest $request): JsonResponse
    {
        try {
            $now = now();
            $reminder = $this->reminderRepository->create($request->validated() + ['created_at' => $now, 'updated_at' => $now]);
            return $this->response->successResponse(
                message: 'Reminder created',
            );
        } catch (Exception $e) {

            return $this->response->errorResponse(
                status: 'Reminder can not be saved',
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Find all the records
     *
     */
    public function findAll(ReminderIndexRequest $request): JsonResponse
    {
        try {
            $data = $this->reminderRepository->findAll(
                columns: ['id', 'title', 'slug', 'description', 'updated_at',],
                perPage: $this->perPage,
                params: $request->validated(),
            );

            return $this->response->paginatedResponse(
                message: 'Reminder found',
                data: $data,
            );
        } catch (Exception $e) {
            return $this->response->errorResponse(
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Update reminder by ID
     *
     */
    public function update(ReminderUpdateRequest $request, int $id): JsonResponse
    {
        try {

            if ($this->reminderRepository->update($id, $request->validated() + ['updated_at' => now()])) {

                return $this->response->successResponse(
                    message: 'Reminder updated',
                );
            } else {

                return $this->response->errorResponse(
                    message: 'Reminder can not be udpated',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }
        } catch (ModelNotFoundException $e) {

            return $this->response->errorResponse(
                message: 'No reminder found',
            );
        }
    }

    /**
     * find reminder by ID
     *
     */
    public function find(int $id): JsonResponse
    {
        try {
            $data = $this->reminderRepository->find($id);
            return $this->response->successResponse(
                message: 'Reminder found',
                data: $data->toArray(),
            );
        } catch (ModelNotFoundException $e) {

            return $this->response->errorResponse(
                message: 'No reminder found',
            );
        } catch (Exception $e) {
            return $this->response->errorResponse(
                status: 'Error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Delete reminder by ID
     *
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $data = $this->reminderRepository->delete($id);
            return $this->response->successResponse(
                message: 'Reminder Found',
            );
        } catch (ModelNotFoundException $e) {
            return $this->response->errorResponse(
                message: 'No reminder found',
            );
        } catch (Exception $e) {
            return $this->response->errorResponse(
                status: 'Error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }
}

?>