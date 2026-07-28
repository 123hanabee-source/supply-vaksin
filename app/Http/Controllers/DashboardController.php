<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * Aggregated counts for the overview panel, scoped by role.
 */
class DashboardController extends BaseController
{
    /** GET /api/dashboard */
    public function index()
    {
        $isAdmin    = $this->isAdmin();
        $facilityId = $this->sessionFacilityId();

        $result = [
            'supplier_count' => DB::table('suppliers')->count(),
            'vaccine_count'  => DB::table('vaccines')->count(),
            'facility_count' => DB::table('facilities')->count(),
            'expiring_soon'  => DB::table('vaccines')
                ->whereRaw('expiry_date - CURRENT_DATE BETWEEN 0 AND 180')
                ->count(),
        ];

        $stockQuery = DB::table('stock');
        if (!$isAdmin && $facilityId) {
            $stockQuery->where('facility_id', $facilityId);
        }
        $result['total_stock'] = (int) $stockQuery->sum('quantity');

        return $this->ok($result);
    }
}
