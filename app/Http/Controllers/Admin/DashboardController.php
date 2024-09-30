<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $data = array(
            'stats_count' => [
                'patient' => [
                    'label' => 'Patients',
                    'icon' => 'la-user-friends',
                    'percentage' => 0,
                ],
                'appointment' => [
                    'label' => 'Appointments',
                    'icon' => 'la-calendar-alt',
                    'percentage' => 0,
                ],
                'consultation' => [
                    'label' => 'Consultations',
                    'icon' => 'la-notes-medical',
                    'percentage' => 0,
                ]
            ]
        );
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(backpack_url('dashboard'));
    }
}
