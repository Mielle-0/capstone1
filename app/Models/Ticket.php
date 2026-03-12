<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';
    protected $primaryKey = 'tck_id';
    public $timestamps = false;

    protected $fillable = [
        'tck_uuid',
        'fbk_id',
        'dep_id',
        'tck_date_created',
        'tck_created_by',
        'tck_date_action',
        'tck_action_by',
        'tck_date_verified',
        'tck_verified_by',
        'tck_disapprove_details',
        'tck_active',
        'tck_rate',
        'tck_rate_date',
    ];

    protected $casts = [
        'tck_date_created' => 'datetime',
        'tck_date_action' => 'datetime',
        'tck_date_verified' => 'datetime',
        'tck_active' => 'integer',
        'tck_rate' => 'integer',
        'tck_rate_date' => 'datetime',
    ];

    /**
     * Relationship with Feedback
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'fbk_id', 'fbk_id');
    }

    /**
     * Relationship with Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dep_id', 'dep_id');
    }

    /**
     * Relationship with User who created the ticket
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'tck_created_by', 'usr_id');
    }

    /**
     * Relationship with User who took action
     */
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'tck_action_by', 'usr_id');
    }

    /**
     * Relationship with User who verified
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'tck_verified_by', 'usr_id');
    }

    // Inside class Ticket
    public function actions()
    {
        return $this->hasMany(Action::class, 'tck_id', 'tck_id');
    }

    /**
     * Scope for active tickets
     */
    public function scopeActive($query)
    {
        return $query->where('tck_active', 1);
    }

    /**
     * Scope for tickets pending action
     */
    public function scopePendingAction($query)
    {
        return $query->whereNull('tck_date_action');
    }

    /**
     * Scope for tickets pending verification
     */
    public function scopePendingVerification($query)
    {
        return $query->whereNotNull('tck_date_action')
                     ->whereNull('tck_date_verified');
    }
}