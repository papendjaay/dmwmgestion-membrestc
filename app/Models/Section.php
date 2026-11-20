<?php

namespace App\Models;

use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Section extends Model
{
    protected $fillable = [
        'conseil_id',
        'Nom',
        'PF1',
        'TEL_PF1',
        'EMAIL_PF1',
        'PF2',
        'TEL_PF2',
        'EMAIL_PF2',
        'PF3',
        'TEL_PF3',
        'EMAIL_PF3',
        'status',
    ];

    public function conseil(): BelongsTo
    {
        return $this->belongsTo(Conseil::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Section $section) {
            $roleName = 'point_focal';
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            $pointsFocaux = [
                ['nom' => $section->PF1, 'tel' => $section->TEL_PF1, 'email' => $section->EMAIL_PF1],
                ['nom' => $section->PF2, 'tel' => $section->TEL_PF2, 'email' => $section->EMAIL_PF2],
                ['nom' => $section->PF3, 'tel' => $section->TEL_PF3, 'email' => $section->EMAIL_PF3],
            ];

            foreach ($pointsFocaux as $pf) {
                $nomPropre = trim((string) $pf['nom']);
                $telephone = trim((string) $pf['tel']);
                $emailSaisi = mb_strtolower(trim((string) $pf['email']));

                if (empty($nomPropre)) {
                    continue;
                }

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
                        'name' => $nomPropre,
                        'email' => $email,
                        'password' => Hash::make($motDePasseTemporaire),
                        'section_id' => $section->id,
                        'is_active' => true,
                    ]);

                    // 1. Envoi Email Gmail si renseigné
                    $emailEnvoye = false;
                    if (! empty($emailSaisi) && filter_var($emailSaisi, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::raw(
                                "Bonjour {$nomPropre},\n\n" .
                                    "Votre compte Point Focal pour la section « {$section->Nom} » sur la plateforme Comité Tamba a été créé.\n\n" .
                                    "Lien de connexion : " . url('/admin') . "\n" .
                                    "Identifiant (Email) : {$email}\n" .
                                    "Mot de passe temporaire : {$motDePasseTemporaire}\n\n" .
                                    "Merci de vous connecter et d'activer la double authentification (2FA).",
                                function ($message) use ($email, $nomPropre) {
                                    $message->to($email, $nomPropre)
                                        ->subject('Vos accès Point Focal - Comité Tamba');
                                }
                            );
                            $emailEnvoye = true;
                        } catch (\Throwable $e) {
                            Log::error('Échec envoi email création Point Focal : ' . $e->getMessage());
                        }
                    }

                    // 2. Envoi WhatsApp si numéro renseigné
                    $whatsappEnvoye = false;
                    if (! empty($telephone)) {
                        $whatsappEnvoye = app(WhatsAppService::class)->envoyerTemplate(
                            telephone: $telephone,
                            templateName: 'creation_compte_point_focal',
                            parametres: [$nomPropre, $email, $motDePasseTemporaire]
                        );
                    }

                    if (Auth::check()) {
                        Notification::make()
                            ->title("Compte Point Focal créé : {$nomPropre}")
                            ->body("Email : {$email} | Mot de passe : {$motDePasseTemporaire}")
                            ->success()
                            ->persistent()
                            ->send();
                    }
                } elseif (! empty($emailSaisi) && $user->email !== $emailSaisi) {
                    if (! User::where('email', $emailSaisi)->where('id', '!=', $user->id)->exists()) {
                        $user->update(['email' => $emailSaisi, 'section_id' => $section->id]);
                    }
                } elseif (! $user->section_id) {
                    $user->update(['section_id' => $section->id]);
                }

                if (! $user->hasRole('super_admin') && ! $user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                }
            }
        });
    }
}
