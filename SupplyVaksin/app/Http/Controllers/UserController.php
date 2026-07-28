<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin  : list all users, create accounts, change anyone's role/facility/
 *          password, delete accounts.
 * Client : can only view and update their OWN basic profile (full_name,
 *          password) — cannot see other users or change role/facility.
 */
class UserController extends BaseController
{
    /** GET — admin only: list every account. */
    public function index()
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        $users = User::with('facility')->orderBy('user_id')->get()->map(fn ($u) => [
            'user_id'       => $u->user_id,
            'username'      => $u->username,
            'role'          => $u->role,
            'full_name'     => $u->full_name,
            'facility_id'   => $u->facility_id,
            'facility_name' => $u->facility?->facility_name,
        ]);
        return $this->ok($users);
    }

    public function show($id)
    {
        if ($this->isClient() && (int) session('user_id') !== (int) $id) {
            return $this->fail('You can only view your own account.', 403);
        }

        $u = User::with('facility')->find($id);
        if (!$u) return $this->fail('User not found.', 404);

        return $this->ok([
            'user_id'       => $u->user_id,
            'username'      => $u->username,
            'role'          => $u->role,
            'full_name'     => $u->full_name,
            'facility_id'   => $u->facility_id,
            'facility_name' => $u->facility?->facility_name,
        ]);
    }

    /** POST — admin only: create a new account. */
    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['user_id', 'username', 'password', 'role'])) return $err;

        $role = $request->input('role');
        if (!in_array($role, ['admin', 'client'])) {
            return $this->fail("Role must be 'admin' or 'client'.", 400);
        }

        User::create([
            'user_id'     => $request->input('user_id'),
            'username'    => $request->input('username'),
            // NOTE: plaintext to match the demo schema — use Hash::make() in production.
            'password'    => $request->input('password'),
            'role'        => $role,
            'full_name'   => $request->input('full_name', ''),
            'facility_id' => $role === 'client' ? $request->input('facility_id') : null,
        ]);
        return $this->ok(null, 'User created.');
    }

    /**
     * PUT — Admin: change username, password, role, facility_id, full_name for anyone.
     * Client: can only update their OWN full_name and password.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return $this->fail('User not found.', 404);

        if ($this->isAdmin()) {
            if ($err = $this->requireFields($request, ['full_name'])) return $err;
            $user->update([
                'full_name'   => $request->input('full_name'),
                'role'        => $request->input('role', $user->role),
                'facility_id' => $request->input('facility_id', $user->facility_id),
                'password'    => $request->input('password', $user->password),
            ]);
            return $this->ok(null, 'User updated.');
        }

        // Client path — own record only, full_name/password only.
        if ((int) session('user_id') !== (int) $id) {
            return $this->fail('You can only update your own account.', 403);
        }

        $updates = [];
        if ($request->filled('full_name')) $updates['full_name'] = $request->input('full_name');
        if ($request->filled('password'))  $updates['password']  = $request->input('password');

        if (empty($updates)) {
            return $this->fail('Nothing to update. You may only change full_name and password.', 400);
        }

        $user->update($updates);
        return $this->ok(null, 'Profile updated.');
    }

    /** DELETE — admin only. */
    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        if ((int) session('user_id') === (int) $id) {
            return $this->fail('You cannot delete your own currently logged-in account.', 400);
        }

        $user = User::find($id);
        if (!$user) return $this->fail('User not found.', 404);

        $user->delete();
        return $this->ok(null, 'User deleted.');
    }
}
