<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Map division name => subdivisions list
        $data = [
            // Centre Region
            'Haute-Sanaga'       => ['Nanga-Eboko', 'Minta', 'Bibey', 'Lembe-Yezoum', 'Mbandjock', 'Nkoteng', 'Shanga'],
            'Lekié'              => ['Monatélé', 'Obala', "Sa'a", 'Batchenga', 'Ebebda', 'Elig-Mfomo', 'Evodoula', 'Okola', 'Lobo'],
            'Mbam-et-Inoubou'   => ['Bafia', 'Bokito', 'Deuk', 'Kon-Yambetta', 'Makénéné', 'Ndikiniméki', 'Nitoukou', 'Ombessa'],
            'Mbam-et-Kim'       => ['Ntui', 'Mbangassina', 'Ngambe-Tikar', 'Ngoro', 'Yoko'],
            'Méfou-et-Afamba'   => ['Mfou', 'Afanloum', 'Awaé', 'Edzendouan', 'Essa', 'Esse', 'Olanguina', 'Soa'],
            'Méfou-et-Akono'    => ['Ngoumou', 'Akono', 'Bikok', 'Mbankomo'],
            'Mfoundi'            => ['Yaoundé I', 'Yaoundé II', 'Yaoundé III', 'Yaoundé IV', 'Yaoundé V', 'Yaoundé VI', 'Yaoundé VII'],
            'Nyong-et-Kéllé'    => ['Éséka', 'Bot-Makak', 'Dibang', 'Messondo', 'Ngog-Mapubi', 'Biyouha', 'Bondjock', 'Matomb', 'Muyuka', 'Nguibassal'],
            'Nyong-et-Mfoumou'  => ['Akonolinga', 'Ayos', 'Endom', 'Kobdombo', 'Mengang'],
            "Nyong-et-So'o"     => ['Mbalmayo', 'Akon-Binga', 'Dzeng', 'Ngomedzap', 'Mengueme'],

            // Littoral Region
            'Moungo'             => ['Nkongsamba I', 'Nkongsamba II', 'Nkongsamba III', 'Bare-Bakem', 'Dibombari', 'Eboné', 'Loum', 'Manjo', 'Melong', 'Mombo', 'Njombé-Penja'],
            'Nkam'               => ['Yabassi', 'Nkondjock', 'Nord-Makombé', 'Yingui'],
            'Sanaga-Maritime'    => ['Edéa I', 'Edéa II', 'Dibamba', 'Dizangué', 'Mouanko', 'Ngambe', 'Massock-Songloulou', 'Ndom', 'Pouma', 'Ngwei'],
            'Wouri'              => ['Douala I', 'Douala II', 'Douala III', 'Douala IV', 'Douala V', 'Douala VI (Manoka)'],

            // West Region
            'Bamboutos'          => ['Mbouda', 'Babadjou', 'Batcham', 'Galim'],
            'Haut-Nkam'         => ['Bafang', 'Bakou', 'Bana', 'Bandja', 'Banka', 'Kekem'],
            'Hauts-Plateaux'    => ['Baham', 'Bamendjou', 'Bangou', 'Batié'],
            'Koung-Khi'         => ['Bandjoun', 'Bayangam', 'Djebem'],
            'Menoua'             => ['Dschang', 'Fokoué', 'Fongo-Tongo', 'Nkong-Ni', 'Penka-Michel', 'Santchou'],
            'Mifi'               => ['Bafoussam I', 'Bafoussam II', 'Bafoussam III'],
            'Ndé'                => ['Bangangté', 'Bassamba', 'Bazou', 'Tonga'],
            'Noun'               => ['Foumban', 'Bangourain', 'Foumbot', 'Kouoptamo', 'Koutaba', 'Magba', 'Malentouen', 'Massangam', 'Njimom'],

            // North-West Region
            'Boyo'               => ['Fundong', 'Belo', 'Bum', 'Njinikom'],
            'Bui'                => ['Kumbo', 'Jakiri', 'Mbven', 'Nkum', 'Noni', 'Oku'],
            'Donga-Mantung'     => ['Nkambé', 'Ako', 'Misaje', 'Ndu', 'Wat'],
            'Menchum'            => ['Wum', 'Benakuma', 'Fungom', 'Furu-Awa'],
            'Mezam'              => ['Bamenda I', 'Bamenda II', 'Bamenda III', 'Bafut', 'Bali', 'Santa', 'Tubah'],
            'Momo'               => ['Mbengwi', 'Andek', 'Batibo', 'Njikwa', 'Widikum-Boffe'],
            'Ngoketunjia'        => ['Ndop', 'Babessi', 'Balikumbat'],

            // South-West Region
            'Fako'               => ['Limbe I', 'Limbe II', 'Limbe III', 'Buea', 'Muyuka', 'Tiko', 'West Coast (Idenau)'],
            'Kupe-Manenguba'    => ['Bangem', 'Nguti', 'Tombel'],
            'Lebialem'           => ['Menji', 'Alou', 'Wabane'],
            'Manyu'              => ['Mamfe', 'Akwaya', 'Eyumodjock', 'Upper Bayang (Tinto)'],
            'Meme'               => ['Kumba I', 'Kumba II', 'Kumba III', 'Konye', 'Mbonge'],
            'Ndian'              => ['Mundemba', 'Bamusso', 'Isanguele', 'Idabato', 'Kombo-Abedimo', 'Kombo-Iditari', 'Ekondo-Titi', 'Toko'],

            // South Region
            'Dja-et-Lobo'       => ['Sangmélima', 'Bengbis', 'Djoum', 'Meyomessala', 'Meyomessi', 'Mintom', 'Oveng', 'Zoétélé'],
            'Mvila'              => ['Ebolowa I', 'Ebolowa II', 'Biwong-Bane', 'Biwong-Bulu', 'Efoulan', 'Mengong', 'Mvangan', 'Ngoulemakong'],
            'Océan'              => ['Kribi I', 'Kribi II', 'Akom II', 'Bipindi', 'Campo', 'Lokoundjé', 'Lolodorf', 'Mvengue', 'Niété'],
            'Vallée-du-Ntem'    => ['Ambam', 'Kyé-Ossi', "Ma'an", 'Olamze'],

            // East Region
            'Boumba-et-Ngoko'   => ['Yokadouma', 'Gari-Gombo', 'Moloundou', 'Salapoumbé'],
            'Haut-Nyong'        => ['Abong-Mbang', 'Bebend', 'Atok', 'Dimako', 'Doumé', 'Lomié', 'Messaména', 'Messok', 'Mindourou', 'Nguelemendouka', 'Ngoyla', 'Somalomo'],
            'Kadey'              => ['Batouri', 'Kentzou', 'Kette', 'Mbang', 'Ndelele', 'Ouli'],
            'Lom-et-Djerem'     => ['Bertoua I', 'Bertoua II', 'Belabo', 'Bétaré-Oya', 'Diang', 'Garoua-Boulaï', 'Mandjou', 'Ngoura'],

            // Adamawa Region
            'Djérem'             => ['Tibati', 'Ngakila'],
            'Faro-et-Déo'       => ['Tignère', 'Galim-Tignère', 'Mayo-Baléo', 'Kontcha'],
            'Mayo-Banyo'        => ['Banyo', 'Bankim', 'Mayo-Darlé'],
            'Mbéré'              => ['Meiganga', 'Djohong', 'Ngaoui', 'Dir'],
            'Vina'               => ['Ngaoundéré I', 'Ngaoundéré II', 'Ngaoundéré III', 'Belel', 'Mbe', 'Nganha', 'Martap', 'Nyambaka'],

            // North Region
            'Bénoué'             => ['Garoua I', 'Garoua II', 'Garoua III', 'Bibemi', 'Dembo', 'Lagdo', 'Pitoa', 'Basheo', 'Touroua'],
            'Faro'               => ['Poli', 'Beka'],
            'Mayo-Louti'        => ['Guider', 'Figuil', 'Mayo-Oulo'],
            'Mayo-Rey'          => ['Tcholliré', 'Madingring', 'Touboro', 'Rey-Bouba'],

            // Far North Region
            'Diamaré'            => ['Maroua I', 'Maroua II', 'Maroua III', 'Bogo', 'Dargala', 'Gawel', 'Meri', 'Ndoukoula', 'Petté'],
            'Logone-et-Chari'   => ['Kousséri', 'Blangoua', 'Darak', 'Fotokol', 'Goulfey', 'Hile-Alifa', 'Logone-Birni', 'Makary', 'Waza', 'Zina'],
            'Mayo-Danay'        => ['Yagoua', 'Datcheka', 'Gobo', 'Guere', 'Kai-Kai', 'Kalfou', 'Kar-Hay', 'Maga', 'Massa', 'Moutourwa', 'Tchatibali', 'Vele', 'Wina'],
            'Mayo-Kani'         => ['Kaélé', 'Dziguilao', 'Guidiguis', 'Mindif', 'Moutourwa', 'Moulvoudaye', 'Touloum'],
            'Mayo-Sava'         => ['Mora', 'Kolofata', 'Tokombéré'],
            'Mayo-Tsanaga'      => ['Mokolo', 'Bourrha', 'Hina', 'Koza', 'Mogodé', 'Mozogo', 'Soulédé-Roua'],
        ];

        foreach ($data as $divisionName => $subdivisions) {
            $divisionId = DB::table('divisions')->where('name', $divisionName)->value('id');

            if (! $divisionId) {
                // Skip silently if division not found (shouldn't happen after 000029)
                continue;
            }

            $rows = [];
            foreach ($subdivisions as $index => $name) {
                $rows[] = [
                    'division_id' => $divisionId,
                    'name'        => $name,
                    'item_number' => $index + 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            DB::table('subdivisions')->insert($rows);
        }
    }

    public function down(): void
    {
        $divisionIds = DB::table('divisions')
            ->whereIn('region_id', DB::table('regions')->where('country_code', 'CM')->pluck('id'))
            ->pluck('id');

        DB::table('subdivisions')->whereIn('division_id', $divisionIds)->delete();
    }
};
