<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class category extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    function shops(): HasMany
    {
          return $this->hasMany(Shop::class);
    }

     function products(): HasMany
    {
          return $this->hasMany(Product::class);
    }
}
