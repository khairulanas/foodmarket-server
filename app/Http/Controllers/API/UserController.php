<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    use PasswordValidationRules;
    public function login(Request $request)
    {
        try {
            // validasi input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // cek credential
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    ['message' => 'Unauthorized'],
                    'Authentication failed',
                    500
                );
            }

            //jika hash password tidak sesuai maka beri error
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credential');
            }

            //jika berhasil maka kirim token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                ['access_token' => $tokenResult, 'token_type' => 'Bearer', 'user' => $user],
                'Authenticated'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                ['message' => 'Something went wrong', 'error' => $e],
                'Authentication Failed',
                500
            );
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' => $request->houseNumber,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                ['access_token' => $tokenResult, 'token_type' => 'Bearer', 'user' => $user],
                'Authenticated'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                ['message' => 'Something went wrong', 'error' => $e],
                'Authentication Failed',
                500
            );
        }
    }
}
