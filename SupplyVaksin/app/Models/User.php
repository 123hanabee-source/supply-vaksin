<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['user_id', 'username', 'password', 'role', 'full_name', 'facility_id', 'email', 'sex', 'date_of_birth', 'assigned_date'];
    protected $hidden = ['password'];
    protected $casts = ['date_of_birth' => 'date', 'assigned_date' => 'date'];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}
