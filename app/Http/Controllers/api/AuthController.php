<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('authToken')->plainTextToken;

        return response([
            'status' => true,
            'message' => 'Registered successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => $user,
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required', // Accepts either username or email
            'password' => 'required|min:6',
        ]);

        // Attempt to find the user by username or email
        $user = User::where(function ($query) use ($request) {
            $query->where('username', $request->login)
                ->orWhere('email', $request->login);
        })->where('status', 'Active')->first();

        if ($user) {
            // Check if the password is correct
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('authToken')->plainTextToken;

                return response([
                    'status' => true,
                    'message' => 'Logged in successfully.',
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'data' => $user
                ], 200);
            }

            return response([
                'status' => false,
                'message' => 'Incorrect password',
            ], 400);
        }

        return response([
            'status' => false,
            'message' => 'This username or email is not registered with us!',
        ], 400);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // Trigger password reset logic (e.g., send email with reset link)
        // Return appropriate response
        return response()->json(['message' => 'Password reset link sent!'], 200);
    }


    // send password recent link 
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to send password reset link.'], 400);
        }
    }


    public function logout(Request $req)
    {
        $user = $req->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'You Logged Out Successfully.'
        ], 200);
    }
}
