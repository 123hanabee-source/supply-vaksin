<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $table = 'distribution';
    protected $primaryKey = 'distribution_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['distribution_id', 'vaccine_id', 'facility_id', 'quantity', 'distribution_date'];
    protected $casts = ['distribution_date' => 'date'];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id', 'vaccine_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}
