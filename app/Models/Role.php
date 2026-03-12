<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'rol_id';

    protected $fillable = [
        'rol_name',
        'rol_active',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'rol_id',
            'usr_id'
        );
    }
}

