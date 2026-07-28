<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

/**
 * Admin  : create, update, delete, list (full access)
 * Client : list only (read-only, no expiry/supplier edits)
 */
class VaccineController extends BaseController
{
    public function index()
    {
        $vaccines = Vaccine::with('supplier')
            ->orderBy('expiry_date')
            ->get()
            ->map(function ($v) {
                $days = now()->diffInDays($v->expiry_date, false);
                $status = $days < 0 ? 'expired'
                    : ($days <= 180 ? 'expiring_soon'
                    : ($days <= 730 ? 'valid' : 'long_shelf_life'));

                return [
                    'vaccine_id'    => $v->vaccine_id,
                    'vaccine_name'  => $v->vaccine_name,
                    'expiry_date'   => $v->expiry_date->toDateString(),
                    'supplier_id'   => $v->supplier_id,
                    'supplier_name' => $v->supplier?->supplier_name,
                    'days_left'     => $days,
                    'status'        => $status,
                ];
            });

        return $this->ok($vaccines);
    }

    public function show($id)
    {
        $vaccine = Vaccine::with('supplier')->find($id);
        if (!$vaccine) return $this->fail('Vaccine not found.', 404);
        return $this->ok($vaccine);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['vaccine_name', 'expiry_date'])) return $err;

        $vaccineId = $request->input('vaccine_id') ?? (Vaccine::max('vaccine_id') ?? 0) + 1;
        try {
            Vaccine::create([
                'vaccine_id'   => $vaccineId,
                'supplier_id'  => $request->input('supplier_id'),
                'vaccine_name' => $request->input('vaccine_name'),
                'expiry_date'  => $request->input('expiry_date'),
            ]);
            AuditLog::log('create', 'vaccines', $vaccineId, 'Created vaccines #' . $vaccineId);
            return $this->ok(null, 'Vaccine added.');
        } catch (QueryException $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['vaccine_name', 'expiry_date'])) return $err;

        $vaccine = Vaccine::find($id);
        if (!$vaccine) return $this->fail('Vaccine not found.', 404);

        $vaccine->update([
            'vaccine_name' => $request->input('vaccine_name'),
            'expiry_date'  => $request->input('expiry_date'),
            'supplier_id'  => $request->input('supplier_id'),
        ]);
        AuditLog::log('update', 'vaccines', $id, 'Updated vaccines #' . $id);
        return $this->ok(null, 'Vaccine updated.');
    }

    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $vaccine = Vaccine::find($id);
        if (!$vaccine) return $this->fail('Vaccine not found.', 404);

        try {
            $vaccine->delete();
            AuditLog::log('delete', 'vaccines', $id, "Deleted vaccine #{$id}");
        return $this->ok(null, 'Vaccine deleted.');
        } catch (QueryException $e) {
            return $this->fail('Cannot delete: vaccine is referenced in distribution or stock records.', 409);
        }
    }
}
