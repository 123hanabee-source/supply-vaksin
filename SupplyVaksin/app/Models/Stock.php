<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'stock_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['stock_id', 'facility_id', 'vaccine_id', 'quantity'];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id', 'vaccine_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}
