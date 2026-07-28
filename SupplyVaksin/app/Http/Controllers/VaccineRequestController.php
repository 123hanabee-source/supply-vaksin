<?php

namespace App\Http\Controllers;

use App\Models\VaccineRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class VaccineRequestController extends BaseController
{
    public function index()
    {
        $query = VaccineRequest::with(['user', 'facility']);

        if ($this->isNurse() && $this->sessionFacilityId()) {
            $query->where('facility_id', $this->sessionFacilityId());
        }

        $rows = $query->orderByDesc('created_at')->get()->map(fn ($r) => [
            'request_id'     => $r->request_id,
            'request_type'   => $r->request_type,
            'vaccine_name'   => $r->vaccine_name,
            'quantity_needed' => $r->quantity_needed,
            'notes'          => $r->notes,
            'status'         => $r->status,
            'admin_notes'    => $r->admin_notes,
            'created_at'     => $r->created_at?->toDateString(),
            'user_id'        => $r->user_id,
            'user_name'      => $r->user?->full_name ?? $r->user?->username,
            'facility_id'    => $r->facility_id,
            'facility_name'  => $r->facility?->facility_name,
        ]);

        return $this->ok($rows);
    }

    public function store(Request $request)
    {
        if (!$this->isNurse()) {
            return $this->fail('Only nurses can submit requests.', 403);
        }

        if ($err = $this->requireFields($request, ['request_type'])) return $err;

        $type = $request->input('request_type');
        if (!in_array($type, ['new_vaccine', 'low_stock', 'other'])) {
            return $this->fail("Type must be 'new_vaccine', 'low_stock', or 'other'.", 400);
        }

        $facilityId = $this->sessionFacilityId();
        if (!$facilityId) {
            return $this->fail('Your account is not linked to a facility.', 400);
        }

        try {
            $maxId = VaccineRequest::max('request_id') ?? 0;
            VaccineRequest::create([
                'request_id'      => $maxId + 1,
                'user_id'         => session('user_id'),
                'facility_id'     => $facilityId,
                'request_type'    => $type,
                'vaccine_name'    => $request->input('vaccine_name'),
                'quantity_needed' => $request->input('quantity_needed'),
                'notes'           => $request->input('notes', ''),
                'status'          => 'pending',
                'admin_notes'     => null,
                'created_at'      => now()->toDateString(),
            ]);
            return $this->ok(null, 'Request submitted successfully.');
        } catch (QueryException $e) {
            return $this->fail('Failed to submit request.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $vr = VaccineRequest::find($id);
        if (!$vr) return $this->fail('Request not found.', 404);

        $status = $request->input('status');
        if ($status && !in_array($status, ['approved', 'rejected'])) {
            return $this->fail("Status must be 'approved' or 'rejected'.", 400);
        }

        $vr->update([
            'status'      => $status ?? $vr->status,
            'admin_notes' => $request->input('admin_notes', $vr->admin_notes),
        ]);

        return $this->ok(null, 'Request updated.');
    }
}
