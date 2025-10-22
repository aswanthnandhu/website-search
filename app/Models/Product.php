<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;
class Product extends Model
{
        use Searchable;
        protected $table = 'products';
        protected $fillable = ['name', 'description', 'category', 'price'];
        public function toSearchableArray()
        {
                return [
                        'name' => $this->name,
                        'description' => $this->description,
                        'category' => $this->category,
                        'price' => $this->price,
                ];
        }
        protected static function booted()
        {
                static::created(function ($model) {
                        UpdateSearchIndex::dispatch($model, 'create');
                });

                static::updated(function ($model) {
                        UpdateSearchIndex::dispatch($model, 'update');
                });

                static::deleted(function ($model) {
                        UpdateSearchIndex::dispatch($model, 'delete');
                });
        }
}
