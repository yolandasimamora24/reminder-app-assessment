<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Reminder;
use App\Jobs\SendMailJob;
use App\Mail\ReminderMail;
use App\Models\User;
use Illuminate\Console\Command;

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
        $now = date("Y-m-d H:i", strtotime(Carbon::now()->addHour()));
        logger($now);

        $reminders = Reminder::get();
        if($reminders !== null){
            //Get all messages that their dispatch date is due
            $reminders->where('date_string',  $now)->each(function($reminder) {
                    $users = User::all();
                    foreach($users as $user) {
                        dispatch(new SendMailJob(
                            $user->email, 
                            new ReminderMail($user, $reminder))
                        );
                    }
                    $reminder->save();   
                
            });
        }
    }
}
