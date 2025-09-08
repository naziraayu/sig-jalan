<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature',
        'action'
    ];

    public function roles()
    {
        return $this->belongsToManyThrough(Role::class, 'role_permission');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }
}
