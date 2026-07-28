<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the SupplyVaksin demo data.
     * Order matters: parents before children (FK dependencies).
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            ['supplier_id' => 1, 'supplier_name' => 'Johnson&Johnson', 'contact' => 'J&J@gmail.com'],
            ['supplier_id' => 2, 'supplier_name' => 'AstraZeneca', 'contact' => ''],
            ['supplier_id' => 3, 'supplier_name' => 'Bavarian Nordic', 'contact' => 'medical.information_NA@bavarian-nordic.com'],
            ['supplier_id' => 4, 'supplier_name' => 'CSL Seqirus', 'contact' => 'customerservice.us@seqirus.com'],
            ['supplier_id' => 5, 'supplier_name' => 'Dynavax', 'contact' => 'dynavaxmedinfo@cencora.com'],
        ]);

        DB::table('facilities')->insert([
            ['facility_id' => 1, 'facility_name' => 'National Association for School Nurses', 'location_' => 'New York'],
            ['facility_id' => 2, 'facility_name' => 'Sabin Vaccine Institute', 'location_' => 'Texas'],
            ['facility_id' => 3, 'facility_name' => 'Task Force for Global Health', 'location_' => 'Finland'],
        ]);

        DB::table('vaccines')->insert([
            ['vaccine_id' => 1, 'supplier_id' => 2, 'vaccine_name' => 'Cholera', 'expiry_date' => '2027-04-13'],
            ['vaccine_id' => 2, 'supplier_id' => 2, 'vaccine_name' => 'Tresivac', 'expiry_date' => '2026-11-02'],
            ['vaccine_id' => 3, 'supplier_id' => 3, 'vaccine_name' => 'Oncovac', 'expiry_date' => '2027-05-07'],
            ['vaccine_id' => 4, 'supplier_id' => 5, 'vaccine_name' => 'Hepatitis B', 'expiry_date' => '2028-01-16'],
            ['vaccine_id' => 5, 'supplier_id' => 4, 'vaccine_name' => 'Influenza', 'expiry_date' => '2026-09-04'],
        ]);

        DB::table('distribution')->insert([
            ['distribution_id' => 1, 'vaccine_id' => 1, 'facility_id' => 2, 'quantity' => 500, 'distribution_date' => '2026-01-10'],
            ['distribution_id' => 2, 'vaccine_id' => 5, 'facility_id' => 1, 'quantity' => 200, 'distribution_date' => '2026-02-14'],
            ['distribution_id' => 3, 'vaccine_id' => 4, 'facility_id' => 3, 'quantity' => 350, 'distribution_date' => '2026-03-01'],
            ['distribution_id' => 4, 'vaccine_id' => 2, 'facility_id' => 1, 'quantity' => 150, 'distribution_date' => '2026-04-20'],
            ['distribution_id' => 5, 'vaccine_id' => 3, 'facility_id' => 2, 'quantity' => 300, 'distribution_date' => '2026-05-05'],
            ['distribution_id' => 6, 'vaccine_id' => 1, 'facility_id' => 3, 'quantity' => 250, 'distribution_date' => '2026-06-11'],
            ['distribution_id' => 7, 'vaccine_id' => 5, 'facility_id' => 2, 'quantity' => 100, 'distribution_date' => '2026-06-25'],
        ]);

        DB::table('stock')->insert([
            ['stock_id' => 1, 'facility_id' => 2, 'vaccine_id' => 1, 'quantity' => 500],
            ['stock_id' => 2, 'facility_id' => 1, 'vaccine_id' => 5, 'quantity' => 200],
            ['stock_id' => 3, 'facility_id' => 3, 'vaccine_id' => 4, 'quantity' => 350],
            ['stock_id' => 4, 'facility_id' => 1, 'vaccine_id' => 2, 'quantity' => 150],
            ['stock_id' => 5, 'facility_id' => 2, 'vaccine_id' => 3, 'quantity' => 300],
            ['stock_id' => 6, 'facility_id' => 3, 'vaccine_id' => 1, 'quantity' => 250],
            ['stock_id' => 7, 'facility_id' => 2, 'vaccine_id' => 5, 'quantity' => 100],
        ]);

        // Admins have facility_id = NULL (system-wide access); clients are linked to one facility each.
        DB::table('users')->insert([
            ['user_id' => 1, 'username' => 'admin', 'password' => 'admin123', 'role' => 'admin', 'full_name' => 'Administrator', 'facility_id' => null],
            ['user_id' => 2, 'username' => 'superadm', 'password' => 'super456', 'role' => 'admin', 'full_name' => 'Super Admin', 'facility_id' => null],
            ['user_id' => 3, 'username' => 'client', 'password' => 'client123', 'role' => 'client', 'full_name' => 'Client User', 'facility_id' => 2],
            ['user_id' => 4, 'username' => 'nurseny', 'password' => 'nurse789', 'role' => 'client', 'full_name' => 'Nurse - New York', 'facility_id' => 1],
            ['user_id' => 5, 'username' => 'sabinrep', 'password' => 'sabin2026', 'role' => 'client', 'full_name' => 'Sabin Representative', 'facility_id' => 2],
            ['user_id' => 6, 'username' => 'tfgh_fin', 'password' => 'finland2026', 'role' => 'client', 'full_name' => 'TFGH Finland Officer', 'facility_id' => 3],
        ]);
    }
}
