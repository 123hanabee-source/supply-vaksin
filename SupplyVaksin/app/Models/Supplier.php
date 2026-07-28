<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['supplier_id', 'supplier_name', 'contact'];

    public function vaccines()
    {
        return $this->hasMany(Vaccine::class, 'supplier_id', 'supplier_id');
    }
}
