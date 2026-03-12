<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $primaryKey = 'fbk_id';
    public $timestamps = false;
    
    protected $fillable = [
        'fbk_uuid',
        'typ_id',
        'thm_id',
        'branch_id',
        'std_id_no',
        'std_name',
        'std_email',
        'std_mobile',
        'std_program',
        'fbk_details',
        'fbk_date_created',
        'fbk_date_validated',
        'fbk_validated_by',
        'fbk_status',
        'fbk_disapprove_details',
        'fbk_created_by',
    ];

    protected $casts = [
        'fbk_date_created' => 'datetime',
        'fbk_date_validated' => 'datetime',
    ];

    // Relationships
    public function type()
    {
        return $this->belongsTo(FeedbackType::class, 'typ_id', 'typ_id');
    }

    public function theme()
    {
        return $this->belongsTo(ThematicValue::class, 'thm_id', 'thm_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'fbk_id', 'fbk_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'fbk_validated_by', 'usr_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'fbk_created_by', 'usr_id');
    }

    public function prediction()
    {
        return $this->hasOne(FeedbackPrediction::class, 'fbk_id', 'fbk_id');
    }
    
    /**
     * Relationship with User who validated
     */
    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'fbk_validated_by', 'usr_id');
    }

    /**
     * Relationship with User who created
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'fbk_created_by', 'usr_id');
    }

    /**
     * Scope for validated feedbacks
     */
    public function scopeValidated($query)
    {
        return $query->where('fbk_status', 1);
    }

    /**
     * Scope for pending feedbacks
     */
    public function scopePending($query)
    {
        return $query->where('fbk_status', 0);
    }

    /**
     * Scope for feedbacks with tickets
     */
    public function scopeWithTickets($query)
    {
        return $query->has('tickets');
    }

    /**
     * Scope for feedbacks without tickets
     */
    public function scopeWithoutTickets($query)
    {
        return $query->doesntHave('tickets');
    }

    // Boot method to auto-generate UUID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($feedback) {
            if (empty($feedback->fbk_uuid)) {
                $feedback->fbk_uuid = (string) Str::uuid();
            }
        });
    }
}
