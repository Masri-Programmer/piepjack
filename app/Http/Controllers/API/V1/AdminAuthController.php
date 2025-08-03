<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\API\V1\LoginRequest;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $request->ensureIsNotRateLimited();

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($request->throttleKey());

            return response()->json([
                'message' => 'Wrong password or email address.',
                'errors' => ['email' => 'Wrong password or email address.'],
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole('Admin')) {
            Auth::logout();
            return response()->json([
                'message' => 'You do not have permission to access this area.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('admin-token')->plainTextToken;

        RateLimiter::clear($request->throttleKey());

        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
            'remember' => $request['remember'],
            'token' => $token,
        ]);
    }

    public function show(Request $request)
    {
        // FIX: Return first_name and last_name instead of name.
        return response()->json($request->user()->only('id', 'first_name', 'last_name', 'email'));
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $user->roles()->attach($adminRole);

        event(new Registered($user));

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request): Response
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response('', 204);
    }
}
