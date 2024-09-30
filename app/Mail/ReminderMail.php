<?php

namespace App\Mail;

use App\Models\Reminder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $access_token;

    /**
     * Create a new message instance.
     *
     * @throws Exception
     */
    public function __construct(protected Reminder $reminder)
    {
        $this->access_token = $this->generateAccessToken();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('reminder@app.com', 'Reminder App'),
            replyTo: [
                new Address('reminder@app.com', 'Reminder App'),
            ],
            subject: 'Reminder App '.$this->reminder->prefix,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder_email',
            with: [
                'description' => $this->reminder->description,
                'timestamp' => strtotime('now'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
