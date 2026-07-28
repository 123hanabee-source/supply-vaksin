<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

/**
 * Admin  : full CRUD across all facilities.
 * Client : read-only view of their own facility's stock. No writes —
 *          stock changes only happen via fulfilled distributions.
 */
class StockController extends BaseController
{
    public function index()
    {
        $query = Stock::with(['facility', 'vaccine']);
        if ($this->isClient() && $this->sessionFacilityId()) {
            $query->where('facility_id', $this->sessionFacilityId());
        }
        $rows = $query->orderBy('stock_id')->get()->map(fn ($s) => [
            'stock_id'      => $s->stock_id,
            'quantity'      => $s->quantity,
            'facility_id'   => $s->facility_id,
            'facility_name' => $s->facility?->facility_name,
            'vaccine_id'    => $s->vaccine_id,
            'vaccine_name'  => $s->vaccine?->vaccine_name,
            'expiry_date'   => $s->vaccine?->expiry_date?->toDateString(),
        ]);
        return $this->ok($rows);
    }

    public function show($id)
    {
        $stock = Stock::with(['facility', 'vaccine'])->find($id);
        if (!$stock) return $this->fail('Stock record not found.', 404);

        if ($this->isClient() && (int) $stock->facility_id !== (int) $this->sessionFacilityId()) {
            return $this->fail("You can only view your own facility's stock.", 403);
        }
        return $this->ok($stock);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['stock_id', 'facility_id', 'vaccine_id', 'quantity'])) return $err;

        Stock::updateOrCreate(
            ['stock_id' => $request->input('stock_id')],
            [
                'facility_id' => $request->input('facility_id'),
                'vaccine_id'  => $request->input('vaccine_id'),
                'quantity'    => $request->input('quantity'),
            ]
        );
        return $this->ok(null, 'Stock saved.');
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['quantity'])) return $err;

        $stock = Stock::find($id);
        if (!$stock) return $this->fail('Stock record not found.', 404);

        $stock->update(['quantity' => $request->input('quantity')]);
        return $this->ok(null, 'Stock updated.');
    }

    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $stock = Stock::find($id);
        if (!$stock) return $this->fail('Stock record not found.', 404);

        $stock->delete();
        return $this->ok(null, 'Stock record deleted.');
    }
}
