<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $reminder;
    protected $user;

    public function __construct($user, $reminder)
    {
        $this->user = $user;
        $this->reminder = $reminder;
    }

    /**
     * Get the email
     */
    public function email(): string
    {
        return Str::title($this->user->email);
    }

    /**
     * Get the prefix
     */
    public function prefix(): string
    {
        return Str::title($this->reminder->prefix);
    }

    /**
     * Get the description
     */
    public function description(): string
    {
        return Str::title($this->reminder->description);
    }

    /**
     * Get the reminder_date
     */
    public function reminder_date(): string
    {
        return Str::title($this->reminder->reminder_date);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reminder_email')
                    ->subject($this->reminder->prefix)
                    ->from('reminder@app.com', 'Reminder App')
                    ->with([
                        'email'=> $this->email(),
                        'prefix' => $this->prefix(),
                        'description' => $this->description(),
                        'reminder_date' => $this->reminder_date(),
                    ]);
    }
}