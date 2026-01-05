<?php

namespace App\Modules\Auth\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendOtpNotification extends Notification
{
    public function __construct(
        private string $otp
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('OTP Code')
            ->line('Your OTP Code:')
            ->line("**{$this->otp}**")
            ->line('Expired in 10 minutes');
    }
}
