<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentPartageNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Document $document) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $auteur = $this->document->user?->name ?? 'Un utilisateur';
        $nomFichier = Str::slug($this->document->titre) . '.pdf';

        $mail = (new MailMessage)
            ->subject("Nouveau document partagé : {$this->document->titre}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("{$auteur} vient de partager un nouveau document PDF sur la plateforme D.M.W.M / COMITÉ TAMBA.")
            ->line("**Titre :** {$this->document->titre}");

        if (! empty($this->document->description)) {
            $mail->line("**Description :** {$this->document->description}");
        }

        if (! empty($this->document->fichier) && Storage::disk('public')->exists($this->document->fichier)) {
            $mail->attachFromStorageDisk('public', $this->document->fichier, $nomFichier, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail
            ->line('Vous trouverez le document PDF directement en pièce jointe de cet e-mail.')
            ->line('Merci de votre attention.');
    }
}
