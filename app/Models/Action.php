<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $table = 'actions';
    protected $primaryKey = 'act_id';
    public $timestamps = false;

    protected $fillable = [
        'act_uuid',
        'tck_id',
        'act_details',
        'act_date_created',
        'act_created_by',
        'act_status',
        'act_reject_details',
        'act_date_verified',
        'act_verified_by',
        'act_active',
        'act_file',
        'act_auto_closed',
        'act_auto_closed_date',
    ];

    protected $casts = [
        'act_date_created' => 'datetime',
        'act_date_verified' => 'datetime',
        'act_auto_closed_date' => 'datetime',
        'act_status' => 'integer',
        'act_active' => 'integer',
        'act_auto_closed' => 'integer',
    ];

    /**
     * Relationship with the Parent Ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'tck_id', 'tck_id');
    }

    /**
     * Relationship with the User who performed the action
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'act_created_by', 'usr_id');
    }

    /**
     * Relationship with the User who verified the action
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'act_verified_by', 'usr_id');
    }

    /**
     * Scope for active actions
     */
    public function scopeActive($query)
    {
        return $query->where('act_active', 1);
    }

    /**
     * Scope for verified actions
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('act_date_verified');
    }

    /**
     * Scope for rejected actions
     */
    public function scopeRejected($query)
    {
        return $query->whereNotNull('act_reject_details');
    }
}