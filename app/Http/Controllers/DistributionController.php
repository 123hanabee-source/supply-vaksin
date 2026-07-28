<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Distribution;
use Illuminate\Http\Request;

/**
 * Admin  : full CRUD across all facilities.
 * Client : read-only on their own facility's incoming shipments, plus one
 *          "basic" action — submitting a restock REQUEST for an admin to
 *          approve later (distribution_date = NULL acts as a pending flag).
 */
class DistributionController extends BaseController
{
    public function index()
    {
        $query = Distribution::with(['vaccine', 'facility']);
        if ($this->isNurse() && $this->sessionFacilityId()) {
            $query->where('facility_id', $this->sessionFacilityId());
        }
        $rows = $query->orderByDesc('distribution_date')->get()->map(fn ($d) => [
            'distribution_id'   => $d->distribution_id,
            'quantity'          => $d->quantity,
            'distribution_date' => $d->distribution_date?->toDateString(),
            'vaccine_id'        => $d->vaccine_id,
            'vaccine_name'      => $d->vaccine?->vaccine_name,
            'facility_id'       => $d->facility_id,
            'facility_name'     => $d->facility?->facility_name,
        ]);
        return $this->ok($rows);
    }

    public function show($id)
    {
        $row = Distribution::with(['vaccine', 'facility'])->find($id);
        if (!$row) return $this->fail('Distribution record not found.', 404);

        if ($this->isNurse() && (int) $row->facility_id !== (int) $this->sessionFacilityId()) {
            return $this->fail('You can only view distributions sent to your facility.', 403);
        }
        return $this->ok($row);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['vaccine_id', 'facility_id', 'quantity'])) return $err;

        $distId = $request->input('distribution_id') ?? (Distribution::max('distribution_id') ?? 0) + 1;
        Distribution::create([
            'distribution_id'   => $distId,
            'vaccine_id'        => $request->input('vaccine_id'),
            'facility_id'       => $request->input('facility_id'),
            'quantity'          => $request->input('quantity'),
            'distribution_date' => $request->input('distribution_date', now()->toDateString()),
        ]);
        return $this->ok(null, 'Distribution recorded.');
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['quantity'])) return $err;

        $row = Distribution::find($id);
        if (!$row) return $this->fail('Distribution record not found.', 404);

        $row->update([
            'quantity'          => $request->input('quantity'),
            'distribution_date' => $request->input('distribution_date'),
            'vaccine_id'        => $request->input('vaccine_id', $row->vaccine_id),
            'facility_id'       => $request->input('facility_id', $row->facility_id),
        ]);
        return $this->ok(null, 'Distribution updated.');
    }

    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $row = Distribution::find($id);
        if (!$row) return $this->fail('Distribution record not found.', 404);

        $row->delete();
        return $this->ok(null, 'Distribution record deleted.');
    }

    /**
     * POST /api/distribution/request
     * Client-only restock request: writes a pending row (distribution_date = NULL)
     * for an admin to later fulfil via update().
     */
    public function requestRestock(Request $request)
    {
        if (!$this->isNurse()) {
            return $this->fail('Only nurses can submit restock requests.', 403);
        }
        if ($err = $this->requireFields($request, ['distribution_id', 'vaccine_id', 'quantity'])) return $err;

        $facilityId = $this->sessionFacilityId();
        if (!$facilityId) {
            return $this->fail('Your account is not linked to a facility.', 400);
        }

        Distribution::create([
            'distribution_id'   => $request->input('distribution_id'),
            'vaccine_id'        => $request->input('vaccine_id'),
            'facility_id'       => $facilityId,
            'quantity'          => $request->input('quantity'),
            'distribution_date' => null,
        ]);
        return $this->ok(null, 'Restock request submitted. An admin will review and fulfil it.');
    }
}
