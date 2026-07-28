<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineRequest extends Model
{
    protected $table = 'vaccine_requests';
    protected $primaryKey = 'request_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['request_id', 'user_id', 'facility_id', 'request_type', 'vaccine_name', 'quantity_needed', 'notes', 'status', 'admin_notes', 'created_at'];
    protected $casts = ['created_at' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}
