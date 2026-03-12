<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackType extends Model
{
    protected $table = 'feedback_types';
    protected $primaryKey = 'typ_id';
    public $timestamps = false;
    
    protected $fillable = ['typ_value'];

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'typ_id', 'typ_id');
    }

    public function thematicValues()
    {
        return $this->hasMany(ThematicValue::class, 'typ_id', 'typ_id');
    }

}
