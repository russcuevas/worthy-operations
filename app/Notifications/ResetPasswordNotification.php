<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Worthy Acosta - Password Reset Request')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . ',')
            ->line('You are receiving this email because we received a password reset request for your account on the Worthy Acosta Inventory & Staff Task Management Portal.')
            ->action('Reset My Password', $resetUrl)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required and your account remains secure.')
            ->salutation("Best regards,\nWorthy Acosta Management Team");
    }
}
