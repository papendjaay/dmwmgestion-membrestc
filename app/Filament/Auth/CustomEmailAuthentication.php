<?php

namespace App\Filament\Auth;

use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;

class CustomEmailAuthentication extends EmailAuthentication
{
    public function sendCode(HasEmailAuthentication $user): bool
    {
        $code = $this->generateCode();

        if (! $code) {
            return false;
        }

        Notification::sendNow($user, new class($code, $this->getCodeExpiryMinutes()) extends VerifyEmailAuthentication {
            public function toMail(object $notifiable): MailMessage
            {
                return (new MailMessage)
                    ->subject('Voici votre code de connexion')
                    ->line('Utilisez le code ci-dessous pour vous connecter à votre espace :')
                    ->line(new HtmlString('
                        <div style="text-align: center; margin: 24px 0;">
                            <span style="display: inline-block; background-color: #f0f9ff; border: 2px solid #0284c7; border-radius: 8px; padding: 14px 28px; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0f172a;">
                                ' . $this->code . '
                            </span>
                        </div>
                    '))
                    ->line("Ce code expirera dans {$this->codeExpiryMinutes} minutes.");
            }
        });

        return true;
    }
}
