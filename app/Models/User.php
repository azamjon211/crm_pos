<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Shop;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_CASHIER = 'cashier';
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
    protected $fillable = [
        'shop_id',
        'name',
        'username',
        'password',
        'role',
        'is_active',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function isSuperAdmin(): bool {
        return $this->role === self::ROLE_SUPERADMIN;
    }
    public function isAdmin(): bool {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isManager(): bool {
        return $this->role === self::ROLE_MANAGER;
    }
    public function isCashier(): bool {
        return $this->role === self::ROLE_CASHIER;
    }
    public function isManagerOrAdmin(): bool {
        return in_array($this->role, [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPERADMIN]);
    }
        public function shop()
        {
        return $this->belongsTo(Shop::class);
        }
}
