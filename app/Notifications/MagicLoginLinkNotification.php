<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MagicLoginLinkNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(10),
            ['user' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject('Votre lien d\'accès à ' . config('app.name'))
            ->view('emails.magic-login', ['url' => $url]);
        // ->markdown('emails.magic-login', ['url' => $url]);
    }
}
