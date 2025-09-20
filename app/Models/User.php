<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool hasPermission(string $action, string $feature)
*/

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'alamat',
        'phone',
        'photo',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function permissions()
    {
        return $this->role?->permissions ?? collect();
    }
    public function hasPermission(string $action, ?string $feature = null): bool
    {
        if (!$this->role) return false;

        $query = $this->role->permissions();

        if ($feature) {
            $query->where('feature', $feature);
        }

        return $query->where('action', $action)->exists();
    }
    public function features()
    {
        if (!$this->role) return collect();

        return $this->role->permissions()->pluck('feature')->unique();
    }

}
