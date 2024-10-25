<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthRequest $request) {
        try{
            $data = $request->validated();
            if(Auth::attempt($data))
            {
                $user = Auth::user();
                $user1 = User::findOrFail($user->id);
                $token = $user1->createToken('main')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => "Success",
                    "token" => $token,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Login failed",
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'Success' => false,
                'message' => "internal server error" . $e,
            ]);
        }
    }

    public function signup(SignInRequest $request) {
        try{
            $data = $request->validated();
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            $token = $user->createToken('main')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => "Success",
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interanl server error' . $e,
            ]);
        }
    }
}
