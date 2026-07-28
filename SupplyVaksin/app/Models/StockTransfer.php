<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $table = 'stock_transfers';
    protected $primaryKey = 'transfer_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'transfer_id', 'vaccine_id', 'from_facility_id', 'to_facility_id',
        'quantity', 'notes', 'status', 'created_by', 'transfer_date',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id', 'vaccine_id');
    }

    public function fromFacility()
    {
        return $this->belongsTo(Facility::class, 'from_facility_id', 'facility_id');
    }

    public function toFacility()
    {
        return $this->belongsTo(Facility::class, 'to_facility_id', 'facility_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
