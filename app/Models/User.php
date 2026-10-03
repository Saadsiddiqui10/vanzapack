<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'must_change_password',
        'customer_group',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ── Authorization helpers ───────────────────────────────────────
    public function hasRole(string|UserRole $role): bool
    {
        $role = $role instanceof UserRole ? $role->value : $role;

        return $this->roles->contains('name', $role);
    }

    public function hasAnyRole(array $roles): bool
    {
        $roles = array_map(fn ($r) => $r instanceof UserRole ? $r->value : $r, $roles);

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole(UserRole::SuperAdmin)) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('name', $permission))
            ->exists();
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(UserRole::staff());
    }

    public function assignRole(string|UserRole $role): void
    {
        $name = $role instanceof UserRole ? $role->value : $role;
        $model = Role::where('name', $name)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$model->id]);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($p) => mb_substr($p, 0, 1))
            ->take(2)
            ->implode('');
    }
}
