<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PokedexTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // if pokedex file does not exist quit
        if(!Storage::disk('csv')->exists('pokedex.csv')) {
            return;
        }
        // get pokedex csv path
        $path = Storage::disk('csv')->path('pokedex.csv');

        // open file and read headers
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);

        // read each row of the file
        while($row = fgetcsv($handle)) {
            $stats = json_decode($row[7]);

            // strip newlines from description if desired
            $description = $row[9];
            if(config('pokedex.strip_description')) {
                $description = str_replace("\n", ' ', $description);
            }

            // insert into the main pokemon table
            DB::table('pokemon')->insert([
                'id' => $row[0],
                'name' => $row[1],
                'height' => $row[3],
                'weight' => $row[4],
                'hp' => $stats->hp,
                'speed' => $stats->speed,
                'attack' => $stats->attack,
                'defense' => $stats->defense,
                'special-attack' => $stats->{'special-attack'},
                'special-defense' => $stats->{'special-defense'},
                'genus' => $row[8],
                'description' => $description
            ]);

            // insert into pokemon_types table
            foreach(json_decode($row[2]) as $type){
                DB::table('pokemon_types')->insert([
                    'id' => $row[0],
                    'type' => $type
                ]);
            }

            // insert into pokemon_abilities table
            foreach(json_decode($row[5]) as $ability){
                DB::table('pokemon_abilities')->insert([
                    'id' => $row[0],
                    'ability' => $ability
                ]);
            }

            // insert into egg_groups table
            foreach(json_decode($row[6]) as $egg_group){
                DB::table('pokemon_egg_groups')->insert([
                    'id' => $row[0],
                    'egg_group' => $egg_group
                ]);
            }
        }

        // close file
        fclose($handle);
    }
}
