<?php

namespace App\Models;

use App\Filament\Resources\Enrolements\EnrolementResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Enrolement extends Model
{
    protected $fillable = [
        'internal_id',
        'Prenom',
        'Nom',
        'Genre',
        'Age',
        'Niveau_Etude',
        'Profession',
        'Conseil',
        'Section',
        'Telephone',
        'Adresse',
        'Type_piece_identite',
        'Numero_piece_identite',
        'Parent',
        'Lien_parental',
        'Contact',
        'Cree_par',
        'Statut',
    ];

    protected static function booted(): void
    {
        static::creating(function (Enrolement $enrolement) {
            // 1. Génération automatique du matricule
            if (empty($enrolement->internal_id)) {
                $year = date('y');
                $nextId = (static::max('id') ?? 0) + 1;
                $enrolement->internal_id = 'MTC' . $year . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }

            // 2. Enregistrement automatique de l'auteur de l'enrôlement
            if (empty($enrolement->Cree_par) && Auth::check()) {
                $enrolement->Cree_par = Auth::user()->name;
            }
        });

        static::saving(function (Enrolement $enrolement) {
            if (blank($enrolement->Type_piece_identite)) {
                $enrolement->Type_piece_identite = 'Pas encore de pièce';
            }

            if (
                $enrolement->Type_piece_identite === 'Pas encore de pièce' ||
                blank($enrolement->Numero_piece_identite)
            ) {
                $enrolement->Numero_piece_identite = 'Pas encore de pièce';
            }
        });

        static::created(function (Enrolement $enrolement) {
            // Envoie une notification dans la cloche de tous les Super Admins
            $superAdmins = User::role('super_admin')->get();

            if ($superAdmins->isNotEmpty()) {
                $auteur = $enrolement->Cree_par ?? 'Un agent';

                Notification::make()
                    ->title('Nouvel enrôlement enregistré')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->iconColor('success')
                    ->body("{$auteur} a enrôlé **{$enrolement->Prenom} {$enrolement->Nom}** ({$enrolement->internal_id}) dans la section **{$enrolement->Section}**.")
                    ->actions([
                        Action::make('voir')
                            ->label('Consulter le dossier')
                            ->url(EnrolementResource::getUrl('view', ['record' => $enrolement]))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($superAdmins);
            }
        });

        static::saved(function (Enrolement $enrolement) {
            $estActive = in_array(
                strtolower(trim((string) $enrolement->Statut)),
                ['actif', 'activé', 'active', '1', 'true', 'oui'],
                true
            );

            if ($estActive) {
                $donnees = Arr::except($enrolement->toArray(), ['id', 'created_at', 'updated_at']);

                DB::table('membres')->updateOrInsert(
                    ['internal_id' => $enrolement->internal_id],
                    $donnees
                );
            } else {
                DB::table('membres')->where('internal_id', $enrolement->internal_id)->delete();
            }
        });
    }
}
