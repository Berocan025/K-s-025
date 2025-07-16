<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Content Section Model
 * Developer: BERAT K
 * Purpose: Anasayfa bölümlerinin sıralama ve yönetim sistemi
 */
class ContentSection extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     * Developer: BERAT K
     */
    protected $table = 'content_sections';

    /**
     * Toplu atama yapılabilir alanlar
     * Developer: BERAT K
     */
    protected $fillable = [
        'section_key',
        'section_name',
        'section_title',
        'section_description',
        'sort_order',
        'is_active',
        'is_visible',
        'section_settings',
        'template_file'
    ];

    /**
     * Cast edilecek alanlar
     * Developer: BERAT K
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'section_settings' => 'array',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Bu bölüme ait içerik öğeleri
     * Developer: BERAT K
     */
    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class, 'section_id')
                    ->orderBy('item_order');
    }

    /**
     * Aktif bölümleri getir
     * Developer: BERAT K
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Görünür bölümleri getir
     * Developer: BERAT K
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Sıralı şekilde getir
     * Developer: BERAT K
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Anasayfa için bölümleri getir
     * Developer: BERAT K
     */
    public function scopeForHomepage($query)
    {
        return $query->active()->visible()->ordered();
    }

    /**
     * Bölüm anahtarına göre ara
     * Developer: BERAT K
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('section_key', $key);
    }

    /**
     * Sıralama numarasını güncelle
     * Developer: BERAT K
     */
    public function updateSortOrder(int $newOrder): bool
    {
        return $this->update(['sort_order' => $newOrder]);
    }

    /**
     * Bölümü aktif/pasif yap
     * Developer: BERAT K
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Bölümü görünür/gizli yap
     * Developer: BERAT K
     */
    public function toggleVisibility(): bool
    {
        return $this->update(['is_visible' => !$this->is_visible]);
    }

    /**
     * Varsayılan bölümleri getir
     * Developer: BERAT K
     */
    public static function getDefaultSections(): array
    {
        return [
            [
                'section_key' => 'platform_services',
                'section_name' => 'Platform Hizmetleri',
                'section_title' => 'Platform Hizmetlerim',
                'section_description' => 'Platform hizmetleri bölümü',
                'sort_order' => 1,
                'is_active' => true,
                'is_visible' => true,
                'template_file' => 'sections.platform-services'
            ],
            [
                'section_key' => 'platforms',
                'section_name' => 'Platformlar',
                'section_title' => 'Platformlarım',
                'section_description' => 'Mevcut platformlar bölümü',
                'sort_order' => 2,
                'is_active' => true,
                'is_visible' => true,
                'template_file' => 'sections.platforms'
            ],
            [
                'section_key' => 'premium_products',
                'section_name' => 'Premium Ürünler',
                'section_title' => 'Premium Ürünlerim',
                'section_description' => 'Premium ürünler bölümü',
                'sort_order' => 3,
                'is_active' => true,
                'is_visible' => true,
                'template_file' => 'sections.premium-products'
            ],
            [
                'section_key' => 'testimonials',
                'section_name' => 'Müşteri Yorumları',
                'section_title' => 'Müşteri Deneyimleri',
                'section_description' => 'Müşteri yorumları bölümü',
                'sort_order' => 4,
                'is_active' => true,
                'is_visible' => true,
                'template_file' => 'sections.testimonials'
            ],
            [
                'section_key' => 'contact_info',
                'section_name' => 'İletişim Bilgileri',
                'section_title' => 'Bize Ulaşın',
                'section_description' => 'İletişim bilgileri bölümü',
                'sort_order' => 5,
                'is_active' => true,
                'is_visible' => true,
                'template_file' => 'sections.contact-info'
            ]
        ];
    }

    /**
     * Sıradaki bölümü getir
     * Developer: BERAT K
     */
    public function getNextSection(): ?self
    {
        return static::where('sort_order', '>', $this->sort_order)
                    ->active()
                    ->visible()
                    ->orderBy('sort_order')
                    ->first();
    }

    /**
     * Önceki bölümü getir
     * Developer: BERAT K
     */
    public function getPreviousSection(): ?self
    {
        return static::where('sort_order', '<', $this->sort_order)
                    ->active()
                    ->visible()
                    ->orderBy('sort_order', 'desc')
                    ->first();
    }

    /**
     * Bölüm için string representation
     * Developer: BERAT K
     */
    public function __toString(): string
    {
        return $this->section_title ?? $this->section_name;
    }
}