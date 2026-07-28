<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\AuditLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class StockTransferController extends BaseController
{
    public function index()
    {
        $query = StockTransfer::with(['vaccine', 'fromFacility', 'toFacility', 'creator']);

        if ($this->isNurse() && $this->sessionFacilityId()) {
            $fid = $this->sessionFacilityId();
            $query->where(function ($q) use ($fid) {
                $q->where('from_facility_id', $fid)
                  ->orWhere('to_facility_id', $fid);
            });
        }

        $rows = $query->orderByDesc('transfer_date')->get()->map(fn ($t) => [
            'transfer_id'     => $t->transfer_id,
            'vaccine_id'      => $t->vaccine_id,
            'vaccine_name'    => $t->vaccine?->vaccine_name,
            'from_facility_id'   => $t->from_facility_id,
            'from_facility_name' => $t->fromFacility?->facility_name,
            'to_facility_id'     => $t->to_facility_id,
            'to_facility_name'   => $t->toFacility?->facility_name,
            'quantity'        => $t->quantity,
            'notes'           => $t->notes,
            'status'          => $t->status,
            'created_by'      => $t->creator?->full_name ?? $t->creator?->username,
            'transfer_date'   => $t->transfer_date?->toDateTimeString(),
        ]);

        return $this->ok($rows);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $fields = ['vaccine_id', 'from_facility_id', 'to_facility_id', 'quantity'];
        if ($err = $this->requireFields($request, $fields)) return $err;

        $vaccineId   = (int) $request->input('vaccine_id');
        $fromId      = (int) $request->input('from_facility_id');
        $toId        = (int) $request->input('to_facility_id');
        $qty         = (int) $request->input('quantity');

        if ($fromId === $toId) {
            return $this->fail('Source and destination facilities must be different.', 400);
        }
        if ($qty <= 0) {
            return $this->fail('Quantity must be at least 1.', 400);
        }

        // Check source stock
        $sourceStock = Stock::where('vaccine_id', $vaccineId)
            ->where('facility_id', $fromId)
            ->first();

        if (!$sourceStock || $sourceStock->quantity < $qty) {
            return $this->fail('Insufficient stock at source facility.', 400);
        }

        try {
            // Deduct from source
            $sourceStock->decrement('quantity', $qty);

            // Add to destination (create if not exists)
            $destStock = Stock::where('vaccine_id', $vaccineId)
                ->where('facility_id', $toId)
                ->first();

            if ($destStock) {
                $destStock->increment('quantity', $qty);
            } else {
                $maxId = Stock::max('stock_id') ?? 0;
                Stock::create([
                    'stock_id'    => $maxId + 1,
                    'vaccine_id'  => $vaccineId,
                    'facility_id' => $toId,
                    'quantity'    => $qty,
                ]);
            }

            // Create transfer record
            $maxTransferId = StockTransfer::max('transfer_id') ?? 0;
            StockTransfer::create([
                'transfer_id'      => $maxTransferId + 1,
                'vaccine_id'       => $vaccineId,
                'from_facility_id' => $fromId,
                'to_facility_id'   => $toId,
                'quantity'         => $qty,
                'notes'            => $request->input('notes', ''),
                'status'           => 'completed',
                'created_by'       => session('user_id'),
                'transfer_date'    => now(),
            ]);

            AuditLog::log('create', 'stock_transfers', $maxTransferId + 1,
                "Transferred {$qty} doses from facility #{$fromId} to #{$toId}");

            return $this->ok(null, 'Transfer completed successfully.');

        } catch (QueryException $e) {
            return $this->fail('Transfer failed: ' . $e->getMessage(), 500);
        }
    }
}
