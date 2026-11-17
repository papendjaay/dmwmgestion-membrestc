<?php

namespace App\Models;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
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

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            if (Auth::check() && ! $document->user_id) {
                $document->user_id = Auth::id();
            }
        });

        static::created(function (Document $document) {
            $auteur = $document->user?->name ?? 'Un utilisateur';
            $autresUtilisateurs = User::where('id', '!=', $document->user_id)->get();

            if ($autresUtilisateurs->isNotEmpty()) {
                Notification::make()
                    ->title('Nouveau document PDF publié')
                    ->body("{$auteur} a partagé le document « {$document->titre} ».")
                    ->icon('heroicon-o-document-text')
                    ->info()
                    ->actions([
                        Action::make('lire')
                            ->label('Lire le PDF')
                            ->url(Storage::url($document->fichier), shouldOpenInNewTab: true),
                    ])
                    ->sendToDatabase($autresUtilisateurs);
            }
        });
    }
}
