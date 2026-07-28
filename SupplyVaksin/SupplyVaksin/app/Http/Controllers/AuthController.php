<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Handles login, logout, and "who am I" session checks.
 */
class AuthController extends BaseController
{
    /** POST /api/login — Body: { "username": "...", "password": "...", "role": "admin|client" } */
    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));
        $role     = trim((string) $request->input('role'));

        if (!$username || !$password || !$role) {
            return $this->fail('Username, password, and role are all required.', 400);
        }

        $user = User::with('facility')
            ->where('username', $username)
            ->where('role', $role)
            ->first();

        // NOTE: plaintext comparison to match the demo schema.
        // Swap for Hash::check($password, $user->password) once passwords are hashed.
        if (!$user || $password !== $user->password) {
            return $this->fail('Invalid username, password, or role.', 401);
        }

        $request->session()->regenerate();
        session([
            'user_id'     => $user->user_id,
            'username'    => $user->username,
            'role'        => $user->role,
            'facility_id' => $user->facility_id,
        ]);

        return $this->ok([
            'user_id'       => $user->user_id,
            'username'      => $user->username,
            'full_name'     => $user->full_name,
            'role'          => $user->role,
            'facility_id'   => $user->facility_id,
            'facility_name' => $user->facility?->facility_name,
            'location'      => $user->facility?->location_,
        ]);
    }

    /** POST /api/logout */
    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        return $this->ok(null, 'Logged out successfully.');
    }

    /** GET /api/me */
    public function me(Request $request)
    {
        if (!session('user_id')) {
            return $this->fail('Not authenticated.', 401);
        }

        $user = User::with('facility')->find(session('user_id'));
        if (!$user) {
            return $this->fail('Not authenticated.', 401);
        }

        return $this->ok([
            'user_id'       => $user->user_id,
            'username'      => $user->username,
            'role'          => $user->role,
            'full_name'     => $user->full_name,
            'facility_id'   => $user->facility_id,
            'facility_name' => $user->facility?->facility_name,
            'location'      => $user->facility?->location_,
        ]);
    }
}
