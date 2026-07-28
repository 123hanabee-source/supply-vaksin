<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'facility_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['facility_id', 'facility_name', 'location_'];

    public function users()
    {
        return $this->hasMany(User::class, 'facility_id', 'facility_id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'facility_id', 'facility_id');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'facility_id', 'facility_id');
    }
}
