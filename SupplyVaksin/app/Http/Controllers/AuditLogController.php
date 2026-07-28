<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends BaseController
{
    public function index()
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $logs = AuditLog::orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->map(fn ($l) => [
                'audit_id'  => $l->audit_id,
                'username'  => $l->username,
                'action'    => $l->action,
                'entity'    => $l->entity,
                'entity_id' => $l->entity_id,
                'details'   => $l->details,
                'created_at' => $l->created_at?->format('Y-m-d H:i:s'),
            ]);

        return $this->ok($logs);
    }
}
