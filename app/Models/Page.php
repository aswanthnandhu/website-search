<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;
class Page extends Model
{
    use Searchable;
    protected $fillable = ['title', 'content'];
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
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
