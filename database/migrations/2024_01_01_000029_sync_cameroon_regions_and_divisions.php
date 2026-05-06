<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cameroonId = DB::table('country_codes')->where('iso_code', 'CM')->value('id');
        if (!$cameroonId) {
            return;
        }

        $data = [
            1 => [
                'name' => 'Centre Region',
                'divisions' => [
                    'Haute-Sanaga', 'Lekié', 'Mbam-et-Inoubou', 'Mbam-et-Kim', 'Méfou-et-Afamba',
                    'Méfou-et-Akono', 'Mfoundi', 'Nyong-et-Kéllé', 'Nyong-et-Mfoumou', 'Nyong-et-So’o',
                ],
            ],
            2 => [
                'name' => 'Littoral Region',
                'divisions' => ['Moungo', 'Nkam', 'Sanaga-Maritime', 'Wouri'],
            ],
            3 => [
                'name' => 'West Region',
                'divisions' => ['Bamboutos', 'Haut-Nkam', 'Hauts-Plateaux', 'Koung-Khi', 'Menoua', 'Mifi', 'Ndé', 'Noun'],
            ],
            4 => [
                'name' => 'North-West Region',
                'divisions' => ['Boyo', 'Bui', 'Donga-Mantung', 'Menchum', 'Mezam', 'Momo', 'Ngoketunjia'],
            ],
            5 => [
                'name' => 'South-West Region',
                'divisions' => ['Fako', 'Kupe-Manenguba', 'Lebialem', 'Manyu', 'Meme', 'Ndian'],
            ],
            6 => [
                'name' => 'South Region',
                'divisions' => ['Dja-et-Lobo', 'Mvila', 'Océan', 'Vallée-du-Ntem'],
            ],
            7 => [
                'name' => 'East Region',
                'divisions' => ['Boumba-et-Ngoko', 'Haut-Nyong', 'Kadey', 'Lom-et-Djerem'],
            ],
            8 => [
                'name' => 'Adamawa Region',
                'divisions' => ['Djérem', 'Faro-et-Déo', 'Mayo-Banyo', 'Mbéré', 'Vina'],
            ],
            9 => [
                'name' => 'North Region',
                'divisions' => ['Bénoué', 'Faro', 'Mayo-Louti', 'Mayo-Rey'],
            ],
            10 => [
                'name' => 'Far North Region',
                'divisions' => ['Diamaré', 'Logone-et-Chari', 'Mayo-Danay', 'Mayo-Kani', 'Mayo-Sava', 'Mayo-Tsanaga'],
            ],
        ];

        $regionIds = [];

        foreach ($data as $regionNumber => $regionInfo) {
            $existingRegion = DB::table('regions')
                ->where('country_code_id', $cameroonId)
                ->where('item_number', $regionNumber)
                ->first();

            if ($existingRegion) {
                DB::table('regions')->where('id', $existingRegion->id)->update([
                    'name' => $regionInfo['name'],
                    'updated_at' => now(),
                ]);
                $regionId = $existingRegion->id;
            } else {
                $regionId = DB::table('regions')->insertGetId([
                    'country_code_id' => $cameroonId,
                    'item_number' => $regionNumber,
                    'name' => $regionInfo['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $regionIds[] = $regionId;

            $divisionIds = [];
            foreach ($regionInfo['divisions'] as $idx => $divisionName) {
                $divisionNumber = $idx + 1;

                $existingDivision = DB::table('divisions')
                    ->where('region_id', $regionId)
                    ->where('item_number', $divisionNumber)
                    ->first();

                if ($existingDivision) {
                    DB::table('divisions')->where('id', $existingDivision->id)->update([
                        'name' => $divisionName,
                        'updated_at' => now(),
                    ]);
                    $divisionId = $existingDivision->id;
                } else {
                    $divisionId = DB::table('divisions')->insertGetId([
                        'region_id' => $regionId,
                        'item_number' => $divisionNumber,
                        'name' => $divisionName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $divisionIds[] = $divisionId;
            }

            DB::table('divisions')
                ->where('region_id', $regionId)
                ->whereNotIn('id', $divisionIds)
                ->delete();

            // Remove placeholder subdivisions seeded earlier; subdivisions can be loaded later from an official list.
            DB::table('subdivisions')->whereIn('division_id', $divisionIds)->delete();
        }

        DB::table('regions')
            ->where('country_code_id', $cameroonId)
            ->whereNotIn('id', $regionIds)
            ->delete();
    }

    public function down(): void
    {
        // No-op: this migration syncs canonical lookup data.
    }
};
