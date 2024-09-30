<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class ResetPasswordNotification extends ResetPassword
{

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable, $email = null)
    {

        $email = $email ?? $notifiable->getEmailForPasswordReset();
        $userData = User::where('email', $email)->first();
        return (new MailMessage)
            ->subject('Reset Password')
            ->view('notifications.reset_password', [
                'token' => route('backpack.auth.password.reset.token', $this->token) . '?email=' . urlencode($email),
                'name' => $userData->full_name,
                'operating_system' => $_SERVER['HTTP_USER_AGENT'],
                'company_name' => config('company.name'),
                'company_address' => config('company.address'),
                'logo' => str_replace('\\', '/', resource_path()) .'/images/logo.jpg',
                'reset_image' => str_replace('\\', '/', resource_path()) .'/images/reset_password.jpg',
                'operating_system' => $_SERVER['HTTP_USER_AGENT'],
            ]);
    }
}
