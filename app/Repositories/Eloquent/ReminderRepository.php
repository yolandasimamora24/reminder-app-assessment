<?php

namespace App\Repositories\Eloquent;

use App\Models\Reminder;
use App\Models\Consultation;
use App\Enums\ConsultationEnum;
use Exception;
use App\Queries\Eloquent\ReminderQuery;
use App\Repositories\Contracts\ReminderInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ReminderRepository implements ReminderInterface
{
    protected $model;
    protected $query;

    /**
     * Create a new repository instance.
     *
     * @param Reminder $model
     */

    public function __construct(Reminder $model, ReminderQuery $query)
    {
        $this->model = $model;
        $this->query = $query;
    }


    /**
     * Returns all the record
     *
     * @param array $columns
     * @param int $perPage
     * @param array $params
     * @return LengthAwarePaginator;
     */

    public function findAll(int $perPage, array $params, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query->search($params)->select($columns)->paginate($perPage);
    }

    /**
     * Save a new record
     *
     * @return bool;
     */

    public function create(array $data): bool
    {
        try {
            $model = new Reminder($data);
            try{
                $saveReminder = $model->save();
            } catch(Exception $e) {
                activity_log('Reminder can not be saved', $e->getMessage(), 'admin/appointment/');
            }
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Delete a user Appointment
     *
     * @param int $id
     * @return bool;
     */

    public function delete(int $id): bool
    {
        try {
            $this->model->findOrFail($id)->delete();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * update reminder by ID
     *
     * @param int $id
     * @param array $values
     * @return bool;
     */

    public function update(int $id, array $values): bool
    {
        return $this->model->findOrFail($id)->update($values);
    }


    /**
     * find reminder by ID
     *
     * @param int $id
     * @return Reminder;
     */
    public function find(int $id): Reminder
    {
        return $this->query->childrenQuery()->findOrFail($id);
    }

}