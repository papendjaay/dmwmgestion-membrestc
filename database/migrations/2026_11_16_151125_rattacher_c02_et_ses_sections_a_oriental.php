<?php

use App\Models\Conseil;
use App\Models\Section;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $oriental = Conseil::where('Code', 'C01')->first();
            $c02 = Conseil::where('Code', 'C02')->first();

            if ($oriental && $c02) {
                // 1. Créer une section à partir du conseil C02 sous Oriental
                Section::create([
                    'conseil_id' => $oriental->id,
                    'Nom'        => $c02->Nom,
                    'PF1'        => $c02->PF1,
                    'TEL_PF1'    => $c02->tel_pf1,
                    'PF2'        => $c02->PF2,
                    'TEL_PF2'    => $c02->tel_pf2,
                    'PF3'        => $c02->PF3,
                    'TEL_PF3'    => $c02->tel_pf3,
                    'status'     => true,
                ]);

                // 2. Rattacher toutes les sections existantes de C02 à Oriental
                Section::where('conseil_id', $c02->id)
                    ->update(['conseil_id' => $oriental->id]);

                // 3. Supprimer l'ancien conseil C02
                $c02->delete();

                // 4. Renommer le code de Boundou (C03) en C02
                Conseil::where('Code', 'C03')->update(['Code' => 'C02']);
            }
        });
    }

    public function down(): void
    {
        // Optionnel : restauration manuelle si nécessaire
    }
};
