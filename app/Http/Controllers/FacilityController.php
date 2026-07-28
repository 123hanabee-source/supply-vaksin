<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

/**
 * Admin  : create, update, delete, list all facilities
 * Client : list only, and only their own facility's detail via show()
 */
class FacilityController extends BaseController
{
    public function index()
    {
        return $this->ok(Facility::orderBy('facility_id')->get());
    }

    public function show($id)
    {
        if ($this->isNurse() && (int) $this->sessionFacilityId() !== (int) $id) {
            return $this->fail("You can only view your own facility.", 403);
        }

        $facility = Facility::find($id);
        if (!$facility) return $this->fail('Facility not found.', 404);
        return $this->ok($facility);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['facility_name'])) return $err;

        $facilityId = $request->input('facility_id') ?? (Facility::max('facility_id') ?? 0) + 1;
        try {
            Facility::create([
                'facility_id' => $facilityId,
                'facility_name' => $request->input('facility_name'),
                'location_'     => $request->input('location_', ''),
            ]);
            return $this->ok(null, 'Facility added.');
        } catch (QueryException $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['facility_name'])) return $err;

        $facility = Facility::find($id);
        if (!$facility) return $this->fail('Facility not found.', 404);

        $facility->update([
            'facility_name' => $request->input('facility_name'),
            'location_'     => $request->input('location_', ''),
        ]);
        return $this->ok(null, 'Facility updated.');
    }

    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $facility = Facility::find($id);
        if (!$facility) return $this->fail('Facility not found.', 404);

        try {
            $facility->delete();
            AuditLog::log('delete', 'facilities', $id, "Deleted facility #{$id}");
        return $this->ok(null, 'Facility deleted.');
        } catch (QueryException $e) {
            return $this->fail('Cannot delete: facility has linked distribution, stock, or user records.', 409);
        }
    }
}
