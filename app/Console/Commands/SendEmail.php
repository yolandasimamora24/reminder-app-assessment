<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Reminder;
use App\Jobs\SendMailJob;
use App\Mail\ReminderMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //One hour is added to compensate for PHP being one hour faster 
        $now = date("Y-m-d H:i", timestamp: strtotime(Carbon::now()->addHour()));
        logger($now);  

        $reminders = Reminder::get();
        if($reminders !== null){
            //Get all messages that their dispatch date is due
            $reminders->where('reminder_date',  $now)->each(function($reminder) {
                    $users = User::all();
                    foreach($users as $user) {
                        dispatch(
                            Mail::to($user->email)->send(new ReminderMail($user, $reminder))
                        );
                    }
                    $reminder->status = 'completed';
                    $reminder->save();   
            });
        }
    }
}
