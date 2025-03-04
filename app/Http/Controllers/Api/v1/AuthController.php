<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $fields['password'] = bcrypt($fields['password']);

        $user = User::create($fields);

        return response([
            'data' => $user,
            'message' => 'User created successfully',


        ],201);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Invalid credentials'
            ],401);

        }

        $token = $user->createToken('personal_access_token')->plainTextToken;

        $response = [
            'data' => $user,
            'token' => $token,
            'message' => 'Logged in successfully',
        ];

        return response($response, 201);
    }


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
