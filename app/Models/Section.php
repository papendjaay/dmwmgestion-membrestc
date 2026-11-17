<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Section extends Model
{
    protected $fillable = [
        'conseil_id',
        'Nom',
        'PF1',
        'TEL_PF1',
        'PF2',
        'TEL_PF2',
        'PF3',
        'TEL_PF3',
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

            $pointsFocaux = array_filter([
                $section->PF1,
                $section->PF2,
                $section->PF3,
            ]);

            foreach ($pointsFocaux as $nomPf) {
                $nomPropre = trim($nomPf);

                if (empty($nomPropre)) {
                    continue;
                }

                $user = User::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($nomPropre)])->first();

                if (! $user) {
                    $slug = Str::slug($nomPropre, '.');
                    $email = "{$slug}@comitetamba.sn";

                    $compteur = 1;
                    while (User::where('email', $email)->exists()) {
                        $email = "{$slug}{$compteur}@comitetamba.sn";
                        $compteur++;
                    }

                    $user = User::create([
                        'name' => $nomPropre,
                        'email' => $email,
                        'password' => Hash::make('PasserMJTC123'),
                    ]);
                }

                if (! $user->hasRole('super_admin') && ! $user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                }
            }
        });
    }
}
