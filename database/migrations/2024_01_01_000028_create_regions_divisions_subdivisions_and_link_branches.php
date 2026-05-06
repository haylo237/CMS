<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_code_id')->constrained('country_codes')->cascadeOnDelete();
            $table->unsignedInteger('item_number');
            $table->string('name');
            $table->timestamps();

            $table->unique(['country_code_id', 'name']);
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->unsignedInteger('item_number');
            $table->string('name');
            $table->timestamps();

            $table->unique(['region_id', 'name']);
        });

        Schema::create('subdivisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->unsignedInteger('item_number');
            $table->string('name');
            $table->timestamps();

            $table->unique(['division_id', 'name']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('country_code_id')->constrained('regions')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('region_id')->constrained('divisions')->nullOnDelete();
            $table->foreignId('subdivision_id')->nullable()->after('division_id')->constrained('subdivisions')->nullOnDelete();
        });

        $cameroonId = DB::table('country_codes')->where('iso_code', 'CM')->value('id');
        if (!$cameroonId) {
            return;
        }

        $regions = [
            1 => 'Adamawa',
            2 => 'Centre',
            3 => 'East',
            4 => 'Far North',
            5 => 'Littoral',
            6 => 'North',
            7 => 'North-West',
            8 => 'West',
            9 => 'South',
            10 => 'South-West',
        ];

        $divisionsByRegion = [
            'Adamawa' => ['Vina', 'Mbere'],
            'Centre' => ['Mfoundi', 'Mefou and Afamba'],
            'East' => ['Haut-Nyong', 'Lom and Djerem'],
            'Far North' => ['Diamare', 'Mayo-Tsanaga'],
            'Littoral' => ['Wouri', 'Moungo'],
            'North' => ['Benoue', 'Faro'],
            'North-West' => ['Mezam', 'Bui'],
            'West' => ['Mifi', 'Menoua'],
            'South' => ['Mvila', 'Ocean'],
            'South-West' => ['Fako', 'Meme'],
        ];

        foreach ($regions as $number => $name) {
            $regionId = DB::table('regions')->insertGetId([
                'country_code_id' => $cameroonId,
                'item_number' => $number,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $divisionNumber = 1;
            foreach ($divisionsByRegion[$name] as $divisionName) {
                $divisionId = DB::table('divisions')->insertGetId([
                    'region_id' => $regionId,
                    'item_number' => $divisionNumber,
                    'name' => $divisionName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('subdivisions')->insert([
                    'division_id' => $divisionId,
                    'item_number' => 1,
                    'name' => $divisionName . ' Subdivision',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $divisionNumber++;
            }
        }
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subdivision_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropConstrainedForeignId('region_id');
        });

        Schema::dropIfExists('subdivisions');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('regions');
    }
};
