<?php

namespace App\Traits;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * CrudPermissionTrait: use Permissions to configure Backpack
 */
trait CrudPermissionTrait
{
    // the operations defined for CRUD controller
    public array $operations = ['list', 'show', 'create', 'update', 'delete'];


    /**
     * set CRUD access using spatie Permissions defined for logged in user
     *
     * @return void
     */
    public function setAccessUsingPermissions()
    {
        // default
        $this->crud->denyAccess($this->operations);

        // get context
        $table = CRUD::getModel()->getTable();
        $modifyTableName = ucwords(substr(str_replace('_', ' ', $table), 0, -1));
        $user = request()->user();

        // double check if no authenticated user
        if (!$user) {
            return; // allow nothing
        }

        // enable operations depending on permission
        foreach ([
            'Read' => ['list'], 
            'Show' => ['show'], 
            'Create' => ['create'], 
            'Modify' => ['update'], 
            'Remove' => ['delete'], 
        ] as $level => $operations) {
            if ($user->can("$level $modifyTableName")) {
                $this->crud->allowAccess($operations);
            }
        }
    }
}