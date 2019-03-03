<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /*
    * Attempts to register a new user.
    */
    public function register(Request $request) {
        // use validator to verify data
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|max:255'
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
            // success
            $data = $validator->getData();
            $token = bin2hex(random_bytes(16));
            // make new user
            $user = new User();
            $user->username = $data["username"];
            $user->password = \Hash::make($data["password"]);
            $user->email = $data["email"];
            $user->api_token = $token;
            // attempt to save the new user
            if($user->save()) {
                // return data
                return response()->json([
                    'message' => 'Successful registration.',
                    'api_token' => $token
                ], 200);
            } else {
                // failure
                return response()->json([
                    'message' => 'Failed to registrate.'
                ], 500);
            }
        }
    }

    /*
    * Gets the user's token and regenerates it when provided with valid credentials.
    */
    public function getToken(Request $request) {
        // use validator to verify data
        $validator = Validator::make($request->all(), [
            'password' => 'required|max:255',
            'username' => 'required|exists:users|max:255'
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
            // success
            $data = $validator->getData();
            $u = User::where('username', $data["username"]);

            // try to get user with username
            if($u->exists()) {
                $user = $u->first();
                if(\Hash::check($data["password"], $user->password)) {
                    // refresh token
                    $token = bin2hex(random_bytes(16));
                    $user->api_token = $token;
                    // attempt to save the updated user
                    if($user->save()) {
                        // return data
                        return response()->json([
                            'message' => 'Regenerated token.',
                            'api_token' => $token
                        ], 200);
                    } else {
                        // failure
                        return response()->json([
                            'message' => 'Failed to regenerate token.'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'message' => 'Invalid password.'
                    ], 422);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid username.'
                ], 422);
            }
        }
    }
}
