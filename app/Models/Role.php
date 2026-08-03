<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }

    public function isCustomer(): bool
    {
        return $this->name === 'customer';
    }

    public function isEmployee(): bool
    {
        return !$this->isCustomer();
    }
}
