<?php

namespace App\Models;

use App\Notifications\DocumentPartageNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'fichier',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Utilisateurs ayant lu ou téléchargé ce document
     */
    public function lecteurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'document_user')
            ->withPivot('lu_le')
            ->orderByPivot('lu_le', 'desc');
    }

    /**
     * Enregistre la lecture pour l'utilisateur connecté (sans écraser la 1ère date de lecture)
     */
    public function marquerCommeLu(?int $userId = null): void
    {
        $userId ??= Auth::id();

        if (! $userId) {
            return;
        }

        $this->lecteurs()->syncWithoutDetaching([
            $userId => ['lu_le' => now()],
        ]);
    }

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            if (Auth::check() && ! $document->user_id) {
                $document->user_id = Auth::id();
            }
        });

        static::created(function (Document $document) {
            $document->loadMissing('user');
            $auteur = $document->user?->name ?? 'Un utilisateur';

            // Marque automatiquement le document comme lu par son auteur
            if ($document->user_id) {
                $document->marquerCommeLu($document->user_id);
            }

            $destinataires = User::query()
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->when($document->user_id, fn($q) => $q->where('id', '!=', $document->user_id))
                ->get();

            if ($destinataires->isNotEmpty()) {
                Notification::make()
                    ->title('Nouveau document partagé')
                    ->body("{$auteur} a partagé le document « {$document->titre} ».")
                    ->icon('heroicon-o-document-text')
                    ->info()
                    ->actions([
                        Action::make('lire')
                            ->label('Ouvrir le document')
                            ->url(Storage::url($document->fichier), shouldOpenInNewTab: true),
                    ])
                    ->sendToDatabase($destinataires);

                NotificationFacade::send($destinataires, new DocumentPartageNotification($document));
            }
        });
    }
}
