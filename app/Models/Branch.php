<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'branch_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['branch_id'];

    public function departments()
    {
        return $this->hasMany(Department::class, 'branch_id', 'branch_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'branch_id', 'branch_id');
    }
}
