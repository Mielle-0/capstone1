<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThematicValue extends Model
{
    protected $table = 'thematic_values';
    protected $primaryKey = 'thm_id';
    public $timestamps = false;
    
    protected $fillable = ['typ_id', 'thm_value'];

    public function type()
    {
        return $this->belongsTo(FeedbackType::class, 'typ_id', 'typ_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'thm_id', 'thm_id');
    }
}
