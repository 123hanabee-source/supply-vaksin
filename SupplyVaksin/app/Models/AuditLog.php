<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'audit_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'audit_id', 'user_id', 'username', 'action',
        'entity', 'entity_id', 'details', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Helper: create an audit log entry
    public static function log(string $action, string $entity, $entityId = null, string $details = null)
    {
        $userId = session('user_id');
        $username = session('username');

        return static::create([
            'audit_id'   => (static::max('audit_id') ?? 0) + 1,
            'user_id'    => $userId,
            'username'   => $username,
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'details'    => $details,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
