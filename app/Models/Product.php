<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
 
class Product extends Model
{
     protected $fillable = ['code', 'name', 'price', 'description','category_id'];
 
     function shops(): BelongsToMany
     {
          return $this->belongsToMany(Shop::class);
     }
 
          function category(): BelongsTo
     {
          return $this->belongsTo(Category::class, 'category_id'); // category_id คือชื่อ FK ในตาราง product
     }
}
 