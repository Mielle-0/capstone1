<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'dep_id';
    public $timestamps = false;
    
    protected $fillable = [
        'dep_name',
        'dep_full_name',
        'branch_id',
        'dep_active'
    
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departments', 'dep_id', 'usr_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'dep_id', 'dep_id');
    }
}
