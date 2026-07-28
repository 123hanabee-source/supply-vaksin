<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $table = 'vaccines';
    protected $primaryKey = 'vaccine_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['vaccine_id', 'supplier_id', 'vaccine_name', 'expiry_date'];
    protected $casts = ['expiry_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
