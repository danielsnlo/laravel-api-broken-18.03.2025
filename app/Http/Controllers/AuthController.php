<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        // Create a token for the user
        $token = $user->createToken($request->name)->plainTextToken;

        // Assign the 'Guest' role by default after registration
        $role = Role::where('name', 'Guest')->first();
        if ($role) {
            $user->roles()->attach($role);  // Assign the Guest role
        }

        // Return the user and token information
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function assignRole(Request $request, $userId)
    {
        // Check if the authenticated user has the 'Admin' role
        $authenticatedUser = $request->user();
        // return $authenticatedUser->hasRole('Admin');
        if (!$authenticatedUser || !$authenticatedUser->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized to assign roles.'], 403);
        }
        // Validate the role input
        $request->validate([
            'role' => 'required|string|in:Admin,Moderator,Guest,Editor',
        ]);

        // Find the user and role to assign
        $user = User::findOrFail($userId);
        $role = Role::where('name', $request->role)->first();

        if ($role) {
            // Sync roles (this will replace any existing roles with the new role)
            $user->roles()->sync([$role->id]);  // You can use ->attach() if you want to add the role without replacing others
            return response()->json(['message' => 'Role assigned successfully.']);
        }

        return response()->json(['message' => 'Role not found.'], 404);
    }

    public function login(Request $request)
    {
        // Validate login credentials
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        // Generate a token for the user
        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'You are logged out.']);
    }
}
