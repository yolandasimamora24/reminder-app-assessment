<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reminder;
use App\Mail\ResultMail;
use App\Jobs\SendMailJob;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\NewArrivals;

class SendEmailController extends Controller
{
    public function getUsers(){

        return User::all();
    }

    public function getMessages(){

        return Reminder::orderBy('created_at', 'desc')->get();
    }
}