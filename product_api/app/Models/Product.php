<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'ProductID';

    // products table has no created_at / updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'ProductName',
        'Description',
        'CategoryID',
        'Barcode',
        'Expiredate',
        'Qty',
        'UnitPriceIn',
        'UnitPriceOut',
        'ProductImage',
        'isDeleted',
    ];

    // Full URL of the image, added to every JSON response
    protected $appends = ['image_url'];

    protected $attributes = [
        'Qty' => 0,
        'UnitPriceIn' => 0,
        'UnitPriceOut' => 0,
        'isDeleted' => 0,
    ];

    protected function casts(): array
    {
        return [
            'CategoryID' => 'integer',
            'Qty' => 'integer',
            'UnitPriceIn' => 'float',
            'UnitPriceOut' => 'float',
            'Expiredate' => 'date:Y-m-d',
            'isDeleted' => 'boolean',
        ];
    }

    /**
     * ProductImage stores a relative path like "products/abc.jpg".
     * image_url gives the client a URL it can load, served by
     * GET /api/product-images/{filename} (so CORS headers are applied).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->ProductImage) {
            return null;
        }

        if (str_starts_with($this->ProductImage, 'http')) {
            return $this->ProductImage;
        }

        return url('api/product-images/'.basename($this->ProductImage));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'CategoryID', 'category_id');
    }
}
