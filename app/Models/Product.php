<?php

namespace App\Models;

use App\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    
    protected $fillable = [
        'image', 'barcode', 'title', 'description', 'buy_price', 'sell_price', 'category_id', 'stock'
    ];
    
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('/storage/products/' . $value),
        );
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
