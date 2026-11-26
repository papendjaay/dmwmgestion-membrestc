<?php

namespace App\Models;

use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Conseil extends Model
{
    protected $fillable = [
        'Nom',
        'Code',
        'pf1',
        'tel_pf1',
        'email_pf1',
        'pf2',
        'tel_pf2',
        'email_pf2',
        'pf3',
        'tel_pf3',
        'email_pf3',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Conseil $conseil) {
            $roleName = 'point_focal_conseil';
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            $pointsFocaux = [
                [
                    'nom'   => $conseil->pf1 ?? $conseil->PF1 ?? null,
                    'tel'   => $conseil->tel_pf1 ?? $conseil->TEL_PF1 ?? null,
                    'email' => $conseil->email_pf1 ?? $conseil->EMAIL_PF1 ?? null,
                ],
                [
                    'nom'   => $conseil->pf2 ?? $conseil->PF2 ?? null,
                    'tel'   => $conseil->tel_pf2 ?? $conseil->TEL_PF2 ?? null,
                    'email' => $conseil->email_pf2 ?? $conseil->EMAIL_PF2 ?? null,
                ],
                [
                    'nom'   => $conseil->pf3 ?? $conseil->PF3 ?? null,
                    'tel'   => $conseil->tel_pf3 ?? $conseil->TEL_PF3 ?? null,
                    'email' => $conseil->email_pf3 ?? $conseil->EMAIL_PF3 ?? null,
                ],
            ];

            foreach ($pointsFocaux as $pf) {
                $nomPropre  = trim((string) $pf['nom']);
                $telephone  = trim((string) $pf['tel']);
                $emailSaisi = mb_strtolower(trim((string) $pf['email']));

                if (empty($nomPropre)) {
                    continue;
                }

                // Recherche d'un compte existant par email ou par nom complet
                $user = ! empty($emailSaisi)
                    ? User::where('email', $emailSaisi)->first()
                    : User::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($nomPropre)])->first();

                if (! $user) {
                    if (! empty($emailSaisi)) {
                        $email = $emailSaisi;
                    } else {
                        $slug = Str::slug($nomPropre, '.');
                        $email = "{$slug}@comitetamba.sn";
                        $compteur = 1;
                        while (User::where('email', $email)->exists()) {
                            $email = "{$slug}{$compteur}@comitetamba.sn";
                            $compteur++;
                        }
                    }

                    $motDePasseTemporaire = Str::password(14);

                    $user = User::create([
                        'name'       => $nomPropre,
                        'email'      => $email,
                        'password'   => Hash::make($motDePasseTemporaire),
                        'conseil_id' => $conseil->id,
                        'is_active'  => true,
                    ]);

                    // 1. Envoi Email Gmail si renseigné
                    if (! empty($emailSaisi) && filter_var($emailSaisi, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::raw(
                                "Bonjour {$nomPropre},\n\n" .
                                    "Votre compte Point Focal de Conseil pour le conseil « {$conseil->Nom} » sur la plateforme Comité Tamba a été créé.\n\n" .
                                    "Lien de connexion : " . url('/admin') . "\n" .
                                    "Identifiant (Email) : {$email}\n" .
                                    "Mot de passe temporaire : {$motDePasseTemporaire}\n\n" .
                                    "Merci de vous connecter et d'activer la double authentification (2FA).",
                                function ($message) use ($email, $nomPropre) {
                                    $message->to($email, $nomPropre)
                                        ->subject('Vos accès Point Focal de Conseil - Comité Tamba');
                                }
                            );
                        } catch (\Throwable $e) {
                            Log::error('Échec envoi email création Point Focal Conseil : ' . $e->getMessage());
                        }
                    }

                    // 2. Envoi WhatsApp si numéro renseigné
                    if (! empty($telephone) && class_exists(WhatsAppService::class)) {
                        try {
                            app(WhatsAppService::class)->envoyerTemplate(
                                telephone: $telephone,
                                templateName: 'creation_compte_point_focal',
                                parametres: [$nomPropre, $email, $motDePasseTemporaire]
                            );
                        } catch (\Throwable $e) {
                            Log::error('Échec envoi WhatsApp création Point Focal Conseil : ' . $e->getMessage());
                        }
                    }

                    if (Auth::check()) {
                        Notification::make()
                            ->title("Compte PF Conseil créé : {$nomPropre}")
                            ->body("Email : {$email} | Mot de passe : {$motDePasseTemporaire}")
                            ->success()
                            ->persistent()
                            ->send();
                    }
                } else {
                    // Met à jour le conseil_id si l'utilisateur existait déjà
                    if ($user->conseil_id !== $conseil->id) {
                        $user->update(['conseil_id' => $conseil->id]);
                    }
                }

                // Attribution du rôle point_focal_conseil
                if (! $user->hasRole('super_admin') && ! $user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                }
            }
        });
    }
}
