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

    public function sendMail(Request $request)
    {

        $message = new Reminder();
        $message->title = $request->title;
        $message->body = $request->body;
        $message->save();

        if($request->item == "now") {

            $message->delivered = 'YES';
            $message->send_date = Carbon::now();
            $message->save();   

            $users = User::all();

            foreach($users as $user) {
                dispatch(new SendMailJob($user->email, new ResultMail($user, $message)));
            }
                
            return response()->json('Mail sent.', 201);

        } else { 

            $message->date_string = date("Y-m-d H:i", strtotime($request->send_date));

            $message->save();   

            return response()->json('Notification will be sent later.', 201);
        }
    }
}