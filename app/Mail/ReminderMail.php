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

    protected $email;
    protected $prefix;
    protected $description;
    protected $reminder_date;
    protected $user;
    protected $reminder;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $prefix, $description, $reminder_date, $user, $reminder)
    {
        $this->email = $email;
        $this->prefix = $prefix;
        $this->description = $description;
        $this->reminder_date = $reminder_date;
        $this->user = $user;
        $this->reminder = $reminder;
    }

    /**
     * Get the email
     */
    public function getEmail(): string
    {
        return Str::title($this->user->email);
    }

    /**
     * Get the prefix
     */
    public function getPrefix(): string
    {
        return Str::title($this->reminder->prefix);
    }

    /**
     * Get the description
     */
    public function getDescription(): string
    {
        return Str::title($this->reminder->description);
    }

    /**
     * Get the reminder_date
     */
    public function getReminderDate(): string
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
                        'email'=> $this->email,
                        'description'=> $this->description,
                        'reminder_date'=> $this->reminder_date,
                    ]);
    }
}