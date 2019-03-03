<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Http\Request;
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

    public function capture(Request $request) {
        // use validator to verify data
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:pokemon'
        ]);

        if ($validator->fails()) {
            // validator failed, return error messages
            $errors = "";
            foreach($validator->errors()->all() as $err) {
                $errors .= $err . " ";
            }
            return response()->json([
                'message' => trim($errors)
            ], 422);
        } else {
            $userId = \Auth::guard('api')->user()->id;
            $pokeId = $validator->getData()['id'];

            $result = DB::table('pokemon_captured')->where('id', $userId)->where('pokemon_id', $pokeId);
            if($result->exists()){
                return response()->json([
                    'message' => "Pokemon {$pokeId} already captured."
                ], 422);
            } else {
                DB::table('pokemon_captured')->insert([
                    ['id' => $userId, 'pokemon_id' => $pokeId]
                ]);
                return response()->json([
                    'message' => "Captured pokemon {$pokeId}."
                ], 200);
            }

        }
    }

    /*
    * Presents a list of pokemon captured by the user.
    */
    public function listCaptured() {
        $userId = \Auth::guard('api')->user()->id;

        return response()->json(
            DB::table('pokemon_captured')->where('id', $userId)->pluck('pokemon_id')
        , 200);
    }

}
