<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'usr_id';
    protected $memoizedRoles;       // Cached Roles
    public $timestamps = false;
    
    protected $fillable = [
        'usr_code',
        'usr_name', 
        'usr_password',
        'usr_active',
    ];
    
    public function getAuthPassword()
    {
        return $this->usr_password;
    }

    /**
     * User ↔ Roles (many-to-many)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles', // pivot table
            'usr_id',     // FK on pivot referencing users
            'rol_id'      // FK on pivot referencing roles
        );
    }

    /**
    * Check if user has at least one of the given roles
    *
    * @param  array|string  $roles
    * @return bool
    */
    public function hasAnyRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];

        // return $this->roles()
        //     ->whereIn('rol_name', $roles)
        //     ->where('rol_active', 1)
        //     ->exists();

        // If role is not cached, call database
        if (!$this->memoizedRoles) {
            $this->memoizedRoles = $this->roles()->where('rol_active', 1)->pluck('rol_name')->toArray();
        }

        // Check the array instead of the database
        return !!array_intersect($roles, $this->memoizedRoles);
    }


    public function departments()
    {
        return $this->belongsToMany(
            Department::class, 
            'user_departments', 
            'usr_id', 
            'dep_id')
            ->withCount(
                ['tickets as pending_tickets_count' => function ($query) {
                $query->whereNull('tck_action_by')
                    ->whereNull('tck_verified_by')
                    ->where('tck_active', 1); 
            }]);
    }

    /**
     * Check if the user has access to a specific department ID
     */
    public function hasDepartment($depId)
    {
        // Check session first (Fastest)
        if (session()->has('user_department_ids')) {
            return in_array($depId, session('user_department_ids'));
        }
        
        return $this->departments()->where('departments.dep_id', $depId)->exists();
    }
    
    // Add to your existing User model
    public function validatedFeedbacks()
    {
        return $this->hasMany(Feedback::class, 'fbk_validated_by', 'usr_id');
    }

    public function createdFeedbacks()
    {
        return $this->hasMany(Feedback::class, 'fbk_created_by', 'usr_id');
    }
}
