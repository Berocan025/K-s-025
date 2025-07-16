<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Admin Permission Model
 * Developer: BERAT K
 * Purpose: Admin kullanıcıları için detaylı yetki sistemi
 */
class AdminPermission extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     * Developer: BERAT K
     */
    protected $table = 'admin_permissions';

    /**
     * Toplu atama yapılabilir alanlar
     * Developer: BERAT K
     */
    protected $fillable = [
        'name',
        'display_name',
        'category',
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
     * Bu yetkiye sahip roller
     * Developer: BERAT K
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Aktif yetkileri getir
     * Developer: BERAT K
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kategoriye göre yetkileri getir
     * Developer: BERAT K
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Yetki adına göre ara
     * Developer: BERAT K
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Mevcut yetki kategorilerini getir
     * Developer: BERAT K
     */
    public static function getCategories(): array
    {
        return [
            'user_management' => 'Kullanıcı Yönetimi',
            'content_management' => 'İçerik Yönetimi',
            'text_management' => 'Metin Yönetimi',
            'content_ordering' => 'İçerik Sıralama',
            'system_settings' => 'Sistem Ayarları',
            'reports' => 'Raporlar',
            'general' => 'Genel Yetkiler'
        ];
    }

    /**
     * Standart admin yetkilerini getir
     * Developer: BERAT K
     */
    public static function getDefaultPermissions(): array
    {
        return [
            // Kullanıcı Yönetimi - Developer: BERAT K
            [
                'name' => 'admin.users.view',
                'display_name' => 'Admin Kullanıcılarını Görüntüle',
                'category' => 'user_management',
                'description' => 'Admin kullanıcı listesini görüntüleyebilir'
            ],
            [
                'name' => 'admin.users.create',
                'display_name' => 'Admin Kullanıcı Oluştur',
                'category' => 'user_management',
                'description' => 'Yeni admin kullanıcı oluşturabilir'
            ],
            [
                'name' => 'admin.users.edit',
                'display_name' => 'Admin Kullanıcı Düzenle',
                'category' => 'user_management',
                'description' => 'Mevcut admin kullanıcıları düzenleyebilir'
            ],
            [
                'name' => 'admin.users.delete',
                'display_name' => 'Admin Kullanıcı Sil',
                'category' => 'user_management',
                'description' => 'Admin kullanıcıları silebilir'
            ],
            
            // İçerik Sıralama - Developer: BERAT K
            [
                'name' => 'content.sections.view',
                'display_name' => 'İçerik Bölümlerini Görüntüle',
                'category' => 'content_ordering',
                'description' => 'Anasayfa bölümlerini görüntüleyebilir'
            ],
            [
                'name' => 'content.sections.reorder',
                'display_name' => 'İçerik Sıralaması Değiştir',
                'category' => 'content_ordering',
                'description' => 'Anasayfa bölümlerinin sırasını değiştirebilir'
            ],
            [
                'name' => 'content.sections.edit',
                'display_name' => 'İçerik Bölümleri Düzenle',
                'category' => 'content_ordering',
                'description' => 'İçerik bölümlerini düzenleyebilir'
            ],
            
            // Metin Yönetimi - Developer: BERAT K
            [
                'name' => 'texts.view',
                'display_name' => 'Site Metinlerini Görüntüle',
                'category' => 'text_management',
                'description' => 'Site metinlerini görüntüleyebilir'
            ],
            [
                'name' => 'texts.edit',
                'display_name' => 'Site Metinlerini Düzenle',
                'category' => 'text_management',
                'description' => 'Site metinlerini düzenleyebilir'
            ],
            [
                'name' => 'texts.categories.manage',
                'display_name' => 'Metin Kategorilerini Yönet',
                'category' => 'text_management',
                'description' => 'Metin kategorilerini yönetebilir'
            ],
            
            // Sistem Ayarları - Developer: BERAT K
            [
                'name' => 'system.settings.view',
                'display_name' => 'Sistem Ayarlarını Görüntüle',
                'category' => 'system_settings',
                'description' => 'Sistem ayarlarını görüntüleyebilir'
            ],
            [
                'name' => 'system.settings.edit',
                'display_name' => 'Sistem Ayarlarını Düzenle',
                'category' => 'system_settings',
                'description' => 'Sistem ayarlarını düzenleyebilir'
            ]
        ];
    }

    /**
     * Yetki için string representation
     * Developer: BERAT K
     */
    public function __toString(): string
    {
        return $this->display_name ?? $this->name;
    }
}