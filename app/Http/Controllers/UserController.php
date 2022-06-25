<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserController extends Controller
{


    /*
        name:test,
        email:test@gmail.com,
        password:147,
        password_confirmation:147
    */
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    /*
        email:test@gmail.com,
        password:147
    */
    public function login(Request $request){
        validator($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ])->validate();


        $user = User::where('email', request('email'))->first();

        if(Hash::check(request('password'), $user->getAuthPassword())){
            return[
                'token' => $user->createToken(time())->plainTextToken
            ];
        }
    }


    /*
        enter token only in header
    */
    public function logout(){

        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }

    /*public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Bad Creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }*/

    /*public function token($id){
        $user = User::find($id);
        return [
            //'message' => $user->tokens->remember_token
            'message' => $user->tokens
        ];
    }*/

}
