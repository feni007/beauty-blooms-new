<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'price', 'stock', 'image_path', 'tint', 'tag', 'option_label', 'options',
        'swatches', 'benefits', 'ingredients', 'how_to_use', 'rating',
        'reviews_count', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'swatches' => 'array',
            'benefits' => 'array',
            'price' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
