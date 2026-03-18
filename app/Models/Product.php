<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'variant_name',
        'description',
        'image',
        'barcode',
        'price',
        'quantity',
        'status',
        'category'
    ];

    public function variants()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    /**
     * Get the total quantity, summing variants if this is a parent.
     */
    public function getTotalQuantityAttribute()
    {
        if ($this->parent_id === null && $this->variants->count() > 0) {
            return $this->variants->sum('quantity');
        }
        return $this->quantity;
    }

    public function getImageUrl()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        if ($this->parent && $this->parent->image) {
            return Storage::url($this->parent->image);
        }
        return asset('images/img-placeholder.jpg');
    }
}
