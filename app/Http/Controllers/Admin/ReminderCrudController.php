<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReminderRequest;
use App\Models\Reminder;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Backpack\CRUD\app\Library\Widget;
use App\Actions\Reminder\StoreReminder;
use Prologue\Alerts\Facades\Alert;
use Exception;
use Carbon\Carbon;
use App\Traits\CrudPermissionTrait;

/**
 * Class ReminderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReminderCrudController extends CrudController
{
    use \Backpack\Pro\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use CrudPermissionTrait;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     */
    public function setup() : void
    {
        CRUD::setModel(Reminder::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reminder');
        CRUD::setEntityNameStrings('reminder', 'reminders');
        //$this->setAccessUsingPermissions();

        Widget::add()
        ->type('style')
        ->content('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css');
        Widget::add()
            ->type('style')
            ->content('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        Widget::add()
            ->type('script')
            ->content('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js');
        Widget::add()->type('script')->content('js/admin/reminder/index.js');
    }

    public function getTotalCount(string $start, string $end): array
    {
        $reminder = Reminder::count();
        return $reminder;
    }

    protected function setupListOperation() : void
    {
        $stats = $this->getReminderStatus();

        Widget::add([
            'type'    => 'div',
            'class'   => 'row',
            'content' => [
                [ 'type' => 'view', 'view' => 'vendor.backpack.ui.widgets.reminder', 'stats' => $stats ],
            ]
        ]);

        if (request()->get('all')) {
            CRUD::setEntityNameStrings('reminder', 'All Reminders');
        }

        if (request()->get('upcoming')) {
            CRUD::setEntityNameStrings('reminder', 'Upcoming Reminders');
            CRUD::addClause('where', 'status', 'pending');
            CRUD::addClause('where', 'reminder_date', '>=', Carbon::parse(now())->startOfDay());
        }

        if (request()->get('completed')) {
            CRUD::setEntityNameStrings('reminder', 'Completed Reminders');
            CRUD::addClause('where', 'status', 'completed');
        }


        CRUD::addColumn([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Email',
            'value' => function ($entry) {
                return $entry->user->email;
            }
        ]);

        CRUD::column('prefix');

        CRUD::column('reminder_date');

        CRUD::orderBy('reminder_date', 'desc');
    }


    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ReminderRequest::class);
        $this->setupCreateUpdateOperation();
    }

    // show whatever you want
    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Email',
            'value' => function ($entry) {
                return $entry->user->email;
            }
        ]);

        // automatically add the columns
        $this->autoSetupShowOperation();

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReminderRequest::class);
        $this->setupCreateUpdateOperation();
    }

        /**
     * Helper function create or update an Reminder
     *
     * @return void
     */
    private function setupCreateUpdateOperation()
    {
        CRUD::addField([
            'name' => 'prefix',
            'label' => 'Prefix',
        ]);

        CRUD::field([
            'name'  => 'description',
            'label' => 'Description',
        ]);

        CRUD::addField([
            'type' => 'relationship',
            'name' => 'email',
            'label' => 'User',
            'ajax' => true,
            'model' => 'App\Models\User',
            'attribute' => 'email',
            'entity' => 'user',
            'relation_type' => 'BelongsTo',
            'inline-create' => true,
            'wrapper' => ['class' => 'form-group col-md-6'],

        ]);

        CRUD::addField([
            'name' => 'reminder_date',
            'type' => 'datetime',
            'label' => 'Reminder Date',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);
    }

    /**
     * This function stores a new reminder in the database.
     */
    public function store(Request $request, StoreReminder $storeReminder) : Redirector|RedirectResponse
    {
        $email = User::find($request['user_id']);
        $request['email'] = $email->email;
        $storeReminder->handle($request);
        return redirect( $this->crud->route);
    }


    public function update(Request $request) : Redirector|RedirectResponse
    {
        try{
            $reminder = Reminder::find($request->id);
            $reminder->reminder_date  = $request->reminder_date;
            $reminder->status  = $request->status;
            $reminder->description  = $request->description;
            $reminder->type  = $request->type;
            $columns = ['reminder_date', 'status', 'type'];
            foreach ($columns as $column) {
                if($reminder->isDirty($column) == true) {
                    $changedColumns[] = $column;
                }
            }
            $reminder = Reminder::whereId($request->id)->update($request->only('reminder_date', 'status', 'description', 'prefix'));
        } catch(Exception $e) {
            Alert::error($e->getMessage())->flash();
        }
        return redirect($this->crud->route);
    }

    protected function getReminderStatus() {
        $total_count = Reminder::count();
        $pending_count = Reminder::where('status', 'pending')->count();
        $completed_count = Reminder::where('status', 'completed')->count();
        $cancelled_count = Reminder::where('status', 'cancelled')->count();

        $start = Carbon::now();
        $end = Carbon::now()->endOfDay();

        $startLastWeek = $end->copy()->subWeek()->startOfDay();
        $endLastWeek = $end->copy()->subWeek()->endOfDay();

        $reminder = Reminder::all();
        $reminderTotal = $reminder->count();
        $reminderNew = $reminder->whereBetween('reminder_date', [$startLastWeek, $endLastWeek])->count();
        $reminderPercentage = ($reminderTotal > 0) ? ($reminderNew / $reminderTotal) * 100 : 0;

        $reminderCompletedTotal = $reminder->where('status', 'completed')->count();
        $completedPercentage = $reminder->whereBetween('reminder_date', [$startLastWeek, $endLastWeek])->where('status', 'completed')->count();
        $completedPercentage = ($reminderTotal > 0  && $completedPercentage > 0) ? ($completedPercentage / $reminderCompletedTotal) * 100 : 0;

        $reminderPendingTotal = $reminder->where('status', 'pending')->count();
        $pendingPercentage = $reminder->whereBetween('reminder_date', [$startLastWeek, $endLastWeek])->where('status', 'pending')->count();
        $pendingPercentage = ($reminderTotal > 0) ? ($pendingPercentage / $reminderPendingTotal) * 100 : 0;
        
        return [
            'Total' => ['count' => $total_count, 'icon' => 'la-notes-total', 'count_percentage' => $reminderPercentage],
            'Completed' => ['count' => $completed_count, 'icon' => 'la-check', 'count_percentage' => $completedPercentage],
            'Pending' => ['count' => $pending_count, 'icon' => 'la-exclamation-circle', 'count_percentage' => $pendingPercentage],
        ];
    }


    /**
     * Search for a user
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Http\JsonResponse
     */
    protected function fetchUser()
    {
        return $this->fetch([
            'model' => User::class,
            'searchable_attributes' => ['email'],
            'paginate' => 10,
            'searchOperator' => 'LIKE',
        ]);
    }

}