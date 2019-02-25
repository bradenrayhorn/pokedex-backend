<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class PokedexController extends Controller {

    /*
    * Gets details for one specific pokemon.
    */
    public function details($id) {
        $result = DB::table('pokemon')->where('id', $id);

        if($result->exists()) {
            // convert result to array
            $pokemon = (array) $result->first();

            // get pokemon types
            $types = array();
            foreach(DB::table('pokemon_types')->where('id', $id)->get() as $row) {
                $types[] = $row->type;
            }
            $pokemon["types"] = $types;

            // get pokemon abilities
            $abilities = array();
            foreach(DB::table('pokemon_abilities')->where('id', $id)->get() as $row) {
                $abilities[] = $row->ability;
            }
            $pokemon["abilities"] = $abilities;

            // get pokemon egg groups
            $egg_groups = array();
            foreach(DB::table('pokemon_egg_groups')->where('id', $id)->get() as $row) {
                $egg_groups[] = $row->egg_group;
            }
            $pokemon["egg_groups"] = $egg_groups;

            // return JSON response
            return response()->json($pokemon, 200);
        } else {
            // pokemon was not found
            return response()->error("Pokemon not found.", 404);
        }
    }

    /*
    * Returns a paginated list of all pokemon.
    */
    public function paginatedPokemon() {
        // get paginated list of pokemon as an array
        $results = DB::table('pokemon')->paginate(config('pokedex.paginate_size'))->toArray();

        $newData = array();
        // step across list injecting details from other database tables
        foreach($results["data"] as $pokemonClass) {
            $pokemon = (array) $pokemonClass;

            $id = $pokemon["id"];

            // get pokemon types
            $types = array();
            foreach(DB::table('pokemon_types')->where('id', $id)->get() as $row) {
                $types[] = $row->type;
            }
            $pokemon["types"] = $types;

            // get pokemon abilities
            $abilities = array();
            foreach(DB::table('pokemon_abilities')->where('id', $id)->get() as $row) {
                $abilities[] = $row->ability;
            }
            $pokemon["abilities"] = $abilities;

            // get pokemon egg groups
            $egg_groups = array();
            foreach(DB::table('pokemon_egg_groups')->where('id', $id)->get() as $row) {
                $egg_groups[] = $row->egg_group;
            }
            $pokemon["egg_groups"] = $egg_groups;

            $newData[] = $pokemon;
        }

        $results["data"] = $newData;

        // return properly formatted results
        return response()->json($results, 200);
    }

}
