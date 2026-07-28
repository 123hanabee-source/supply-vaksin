<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            ['audit_id' => 1, 'user_id' => 1, 'username' => 'admin', 'action' => 'create', 'entity' => 'vaccines', 'entity_id' => 1, 'details' => 'Cholera', 'created_at' => '2026-07-25 09:00:00'],
            ['audit_id' => 2, 'user_id' => 1, 'username' => 'admin', 'action' => 'create', 'entity' => 'vaccines', 'entity_id' => 2, 'details' => 'Hepatitis B', 'created_at' => '2026-07-25 09:05:00'],
            ['audit_id' => 3, 'user_id' => 1, 'username' => 'admin', 'action' => 'create', 'entity' => 'suppliers', 'entity_id' => 1, 'details' => 'Pfizer', 'created_at' => '2026-07-26 10:00:00'],
            ['audit_id' => 4, 'user_id' => 1, 'username' => 'admin', 'action' => 'update', 'entity' => 'vaccines', 'entity_id' => 1, 'details' => 'Updated expiry date', 'created_at' => '2026-07-27 14:30:00'],
            ['audit_id' => 5, 'user_id' => 1, 'username' => 'admin', 'action' => 'create', 'entity' => 'distribution', 'entity_id' => 1, 'details' => 'Cholera → National Assoc. for School Nurses', 'created_at' => '2026-07-28 08:15:00'],
        ];

        foreach ($logs as $log) {
            DB::table('audit_logs')->insert($log);
        }
    }
}
