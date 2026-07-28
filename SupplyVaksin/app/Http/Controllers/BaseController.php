<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Shared helpers every API controller extends: JSON responses,
 * input parsing, and role checks against the current session.
 */
abstract class BaseController extends Controller
{
    /** Send a JSON success response. */
    protected function ok($data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $payload = ['success' => true];
        if ($message !== null) $payload['message'] = $message;
        if ($data !== null)    $payload['data'] = $data;
        return response()->json($payload, $status);
    }

    /** Send a JSON error response with a given HTTP status. */
    protected function fail(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }

    /** True if the current session belongs to an admin. */
    protected function isAdmin(): bool
    {
        return session('role') === 'admin';
    }

    /** True if the current session belongs to a client. */
    protected function isClient(): bool
    {
        return session('role') === 'client';
    }

    /** The logged-in client's facility_id, or null. */
    protected function sessionFacilityId(): ?int
    {
        return session('facility_id');
    }

    /** Throws a 401 JSON response (handled by middleware) — kept for inline checks. */
    protected function requireAdminOrFail(): ?JsonResponse
    {
        return $this->isAdmin() ? null : $this->fail('Admin access required for this action.', 403);
    }

    /** Require a set of fields to be present and non-empty in the request body. */
    protected function requireFields(Request $request, array $fields): ?JsonResponse
    {
        foreach ($fields as $f) {
            $value = $request->input($f);
            if ($value === null || $value === '') {
                return $this->fail("Missing required field: $f", 400);
            }
        }
        return null;
    }
}
