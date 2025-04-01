<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);
        
        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];

        // Assign 'Guest' role
        $role = Role::where('name', 'Guest')->first();
        $user->roles()->attach($role);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    public function assignRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string|in:Admin,Moderator,Guest,Editor',
        ]);
    
        $user = User::findOrFail($userId);
        $role = Role::where('name', $request->role)->first();
    
        if ($role) {
            $user->roles()->sync([$role->id]); // Or use ->attach() for adding one role
            return response()->json(['message' => 'Role assigned successfully.']);
        }
    
        return response()->json(['message' => 'Role not found.'], 404);
    }
    
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['message' => 'The provided creadentials are incorrect.'];
        }
        
        $token = $user->createToken($user->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return ['message' => 'You are logged out.'];
    }
}
