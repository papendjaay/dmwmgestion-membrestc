<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bloc 1 : Identité & Coordonnées
                InfoSection::make('Identité & Coordonnées')
                    ->description('Informations personnelles et coordonnées de connexion de l\'utilisateur.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nom complet')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->placeholder('Non renseigné'),

                        TextEntry::make('email')
                            ->label('Adresse e-mail')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->copyMessage('Adresse e-mail copiée !')
                            ->placeholder('Non renseigné'),

                        TextEntry::make('email_verified_at')
                            ->label('Vérification e-mail')
                            ->badge()
                            ->getStateUsing(fn($record): bool => filled($record->email_verified_at))
                            ->formatStateUsing(fn(bool $state): string => $state ? 'Vérifié' : 'Non vérifié')
                            ->icon(fn(bool $state) => $state ? Heroicon::OutlinedCheckBadge : Heroicon::OutlinedClock)
                            ->color(fn(bool $state): string => $state ? 'success' : 'warning'),
                    ]),

                // Bloc 2 : Rôle, Rattachement & Sécurité
                InfoSection::make('Rôle, Rattachement & Sécurité')
                    ->description('Niveau d\'accès, périmètre géographique et double authentification.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('role')
                            ->label('Rôle attribué')
                            ->getStateUsing(fn($record) => $record->getRoleNames()->first() ?? 'User')
                            ->badge()
                            ->color(fn($state) => match (strtolower(str_replace(['-', '_'], ' ', (string) $state))) {
                                'super admin'      => 'warning',
                                'point focal'      => 'info',
                                'pf conseil'       => 'success',
                                'pf section'       => 'primary',
                                'admin', 'admin 1' => 'danger',
                                default            => 'gray',
                            })
                            ->formatStateUsing(fn($state) => str($state)->replace(['_', '-'], ' ')->title()),

                        TextEntry::make('rattachement')
                            ->label('Conseil / Section')
                            ->getStateUsing(function ($record) {
                                $section = $record->getMaSection();

                                if (! $section) {
                                    return 'Tous / Global';
                                }

                                $extractLabel = function ($model) {
                                    if (! $model) {
                                        return null;
                                    }
                                    $attrs = collect($model->getAttributes())->except([
                                        'id',
                                        'conseil_id',
                                        'user_id',
                                        'PF1',
                                        'PF2',
                                        'PF3',
                                        'pf1',
                                        'pf2',
                                        'pf3',
                                        'created_at',
                                        'updated_at',
                                    ]);

                                    return $attrs->first(fn($val) => is_string($val) && trim($val) !== '');
                                };

                                $nomConseil = $extractLabel($section->conseil);
                                $nomSection = $extractLabel($section);

                                if ($nomConseil && $nomSection) {
                                    return "{$nomConseil} — {$nomSection}";
                                }

                                return $nomSection ?: ($nomConseil ?: 'Tous / Global');
                            })
                            ->badge()
                            ->color(fn($state) => $state === 'Tous / Global' ? 'gray' : 'warning'),

                        TextEntry::make('has_email_authentication')
                            ->label('Double authentification (2FA)')
                            ->badge()
                            ->getStateUsing(fn($record): bool => (bool) $record->has_email_authentication)
                            ->formatStateUsing(fn(bool $state): string => $state ? 'Activée' : 'Désactivée')
                            ->icon(fn(bool $state) => $state ? Heroicon::OutlinedShieldCheck : Heroicon::OutlinedShieldExclamation)
                            ->color(fn(bool $state): string => $state ? 'success' : 'warning'),
                    ]),

                // Bloc 3 : Historique du compte
                InfoSection::make('Historique du compte')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Compte créé le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Dernière mise à jour')
                            ->dateTime('d/m/Y à H:i')
                            ->icon(Heroicon::OutlinedClock)
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
