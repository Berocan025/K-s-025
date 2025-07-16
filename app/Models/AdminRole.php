<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Admin Role Model
 * Developer: BERAT K
 * Purpose: Admin kullanıcıları için rol yönetimi sistemi
 */
class AdminRole extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     * Developer: BERAT K
     */
    protected $table = 'admin_roles';

    /**
     * Toplu atama yapılabilir alanlar
     * Developer: BERAT K
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active'
    ];

    /**
     * Cast edilecek alanlar
     * Developer: BERAT K
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Bu role sahip kullanıcılar
     * Developer: BERAT K
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'admin_role_id');
    }

    /**
     * Bu role atanmış yetkiler
     * Developer: BERAT K
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AdminPermission::class, 'role_permissions', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Aktif rolleri getir
     * Developer: BERAT K
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Rol adına göre ara
     * Developer: BERAT K
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Bu rolün belirli bir yetkiye sahip olup olmadığını kontrol et
     * Developer: BERAT K
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()
                    ->where('name', $permissionName)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Bu role yetki ata
     * Developer: BERAT K
     */
    public function givePermission(AdminPermission|string $permission): self
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('name', $permission)->first();
        }

        if ($permission && !$this->hasPermission($permission->name)) {
            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    /**
     * Bu rolden yetki kaldır
     * Developer: BERAT K
     */
    public function revokePermission(AdminPermission|string $permission): self
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }

        return $this;
    }

    /**
     * Rol için string representation
     * Developer: BERAT K
     */
    public function __toString(): string
    {
        return $this->display_name ?? $this->name;
    }
}