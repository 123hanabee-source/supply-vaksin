<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferSeeder extends Seeder
{
    public function run(): void
    {
        $transfers = [
            ['transfer_id' => 1, 'vaccine_id' => 1, 'from_facility_id' => 1, 'to_facility_id' => 2, 'quantity' => 200, 'notes' => 'Emergency redistribution for cholera outbreak', 'status' => 'completed', 'created_by' => 1, 'transfer_date' => '2026-07-26 10:30:00'],
            ['transfer_id' => 2, 'vaccine_id' => 2, 'from_facility_id' => 2, 'to_facility_id' => 3, 'quantity' => 150, 'notes' => 'Routine stock balancing', 'status' => 'completed', 'created_by' => 1, 'transfer_date' => '2026-07-27 14:00:00'],
            ['transfer_id' => 3, 'vaccine_id' => 3, 'from_facility_id' => 1, 'to_facility_id' => 3, 'quantity' => 100, 'notes' => 'Low stock replenishment at Finland facility', 'status' => 'completed', 'created_by' => 1, 'transfer_date' => '2026-07-28 09:15:00'],
        ];

        foreach ($transfers as $t) {
            DB::table('stock_transfers')->insert($t);
        }
    }
}
