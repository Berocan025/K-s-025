<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Content Item Model
 * Developer: BERAT K
 * Purpose: İçerik bölümlerindeki öğelerin yönetimi
 */
class ContentItem extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     * Developer: BERAT K
     */
    protected $table = 'content_items';

    /**
     * Toplu atama yapılabilir alanlar
     * Developer: BERAT K
     */
    protected $fillable = [
        'section_id',
        'item_title',
        'item_description',
        'item_image',
        'item_link',
        'item_button_text',
        'item_order',
        'is_active',
        'item_meta'
    ];

    /**
     * Cast edilecek alanlar
     * Developer: BERAT K
     */
    protected $casts = [
        'is_active' => 'boolean',
        'item_order' => 'integer',
        'item_meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Bu öğenin ait olduğu bölüm
     * Developer: BERAT K
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }

    /**
     * Aktif öğeleri getir
     * Developer: BERAT K
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sıralı şekilde getir
     * Developer: BERAT K
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('item_order');
    }

    /**
     * Belirli bölüme ait öğeleri getir
     * Developer: BERAT K
     */
    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Bölüm anahtarına göre öğeleri getir
     * Developer: BERAT K
     */
    public function scopeBySectionKey($query, $sectionKey)
    {
        return $query->whereHas('section', function($q) use ($sectionKey) {
            $q->where('section_key', $sectionKey);
        });
    }

    /**
     * Öğe sırasını güncelle
     * Developer: BERAT K
     */
    public function updateItemOrder(int $newOrder): bool
    {
        return $this->update(['item_order' => $newOrder]);
    }

    /**
     * Öğeyi aktif/pasif yap
     * Developer: BERAT K
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Öğe meta bilgisini güncelle
     * Developer: BERAT K
     */
    public function updateMeta(string $key, $value): bool
    {
        $meta = $this->item_meta ?? [];
        $meta[$key] = $value;
        return $this->update(['item_meta' => $meta]);
    }

    /**
     * Meta bilgisini getir
     * Developer: BERAT K
     */
    public function getMeta(string $key, $default = null)
    {
        return $this->item_meta[$key] ?? $default;
    }

    /**
     * Sıradaki öğeyi getir
     * Developer: BERAT K
     */
    public function getNextItem(): ?self
    {
        return static::where('section_id', $this->section_id)
                    ->where('item_order', '>', $this->item_order)
                    ->active()
                    ->orderBy('item_order')
                    ->first();
    }

    /**
     * Önceki öğeyi getir
     * Developer: BERAT K
     */
    public function getPreviousItem(): ?self
    {
        return static::where('section_id', $this->section_id)
                    ->where('item_order', '<', $this->item_order)
                    ->active()
                    ->orderBy('item_order', 'desc')
                    ->first();
    }

    /**
     * Resim URL'sini getir
     * Developer: BERAT K
     */
    public function getImageUrl(): ?string
    {
        if (!$this->item_image) {
            return null;
        }

        // Eğer tam URL ise direkt döndür
        if (filter_var($this->item_image, FILTER_VALIDATE_URL)) {
            return $this->item_image;
        }

        // Yerel dosya ise storage URL'si oluştur
        return asset('storage/' . $this->item_image);
    }

    /**
     * Link URL'sini getir (güvenli)
     * Developer: BERAT K
     */
    public function getLinkUrl(): ?string
    {
        if (!$this->item_link) {
            return null;
        }

        // Eğer tam URL ise direkt döndür
        if (filter_var($this->item_link, FILTER_VALIDATE_URL)) {
            return $this->item_link;
        }

        // Yerel link ise route oluştur
        return url($this->item_link);
    }

    /**
     * Öğenin kısa açıklamasını getir
     * Developer: BERAT K
     */
    public function getShortDescription(int $limit = 150): string
    {
        if (!$this->item_description) {
            return '';
        }

        return strlen($this->item_description) > $limit 
            ? substr($this->item_description, 0, $limit) . '...'
            : $this->item_description;
    }

    /**
     * Öğe için string representation
     * Developer: BERAT K
     */
    public function __toString(): string
    {
        return $this->item_title;
    }
}