<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserResponse extends Model
{
    use HasFactory;

    protected $table = 'user_responses';
    protected $primaryKey = 'res_id';
    public $timestamps = false;

    protected $fillable = [
        'res_id',
        'tck_uuid',
        'res_message',
        'res_date_created',
    ];

    protected $casts = [
        'res_date_created' => 'datetime',
        'res_active' => 'integer',
    ];

    /**
     * Relationship with the Parent Ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'tck_uuid', 'tck_uuid');
    }
    
    // Boot method to auto-generate UUID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($response) {
            if (empty($response->res_uuid)) {
                $response->res_uuid = (string) Str::uuid();
            }
            if (empty($response->res_date_created)) {
                $response->res_date_created = now();
            }
        });
    }
}