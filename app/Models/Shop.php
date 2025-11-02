<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Shop extends Model
{
    protected $fillable = ['code', 'name', 'owner', 'latitude', 'longitude', 'address'];

    function products(): BelongsToMany 
    {
          return $this->belongsToMany(Product::class)->withTimestamps();
    }

        function category(): BelongsTo
        {
            return $this->belongsTo(Category::class);
        }
}