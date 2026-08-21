<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReturn extends Model
{
    use HasFactory;

    protected $table = 'ticket_returns';

    // Disable standard timestamps since the schema only uses 'returned_at'
    public $timestamps = false;

    protected $fillable = [
        'tck_id',
        'fbk_id',
        'returned_by_usr_id',
        'routing_source',
        'return_reason',
        'returned_at',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    /**
     * Relationship: The ticket that was returned.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'tck_id', 'tck_id');
    }

    /**
     * Relationship: The parent feedback associated with the returned ticket.
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'fbk_id', 'fbk_id');
    }

    /**
     * Relationship: The department user/staff who rejected and returned the ticket.
     */
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by_usr_id', 'usr_id');
    }
}