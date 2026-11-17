<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConseilAndSectionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('sections')->truncate();
        Schema::enableForeignKeyConstraints();

        $sections = [
            // Conseil Oriental (conseil_id = 1)
            ['conseil_id' => 1, 'Nom' => 'Abattoirs'],
            ['conseil_id' => 1, 'Nom' => 'Adjaf'],
            ['conseil_id' => 1, 'Nom' => 'Camp Navétanes'],
            ['conseil_id' => 1, 'Nom' => 'Dépôt'],
            ['conseil_id' => 1, 'Nom' => 'Gourel'],
            ['conseil_id' => 1, 'Nom' => 'Kédougou'],
            ['conseil_id' => 1, 'Nom' => 'Liberté'],
            ['conseil_id' => 1, 'Nom' => 'Médina Coura'],
            ['conseil_id' => 1, 'Nom' => 'Plateau'],
            ['conseil_id' => 1, 'Nom' => 'Pont'],
            ['conseil_id' => 1, 'Nom' => 'Quinzambougou'],
            ['conseil_id' => 1, 'Nom' => 'Saré Guilèle'],

            // Conseil Koussanar (conseil_id = 2)
            ['conseil_id' => 2, 'Nom' => 'Koussanar'],
            ['conseil_id' => 2, 'Nom' => 'Sinthiou Malème'],

            // Conseil Boundou (conseil_id = 3)
            ['conseil_id' => 3, 'Nom' => 'Goudiry'],
            ['conseil_id' => 3, 'Nom' => 'Kothiary'],
            ['conseil_id' => 3, 'Nom' => 'Koulor'],
            ['conseil_id' => 3, 'Nom' => 'Nguidowol'],
            ['conseil_id' => 3, 'Nom' => 'Séno'],
        ];

        foreach ($sections as $section) {
            Section::create([
                'conseil_id' => $section['conseil_id'],
                'Nom' => $section['Nom'],
                'status' => 1,
            ]);
        }
    }
}
