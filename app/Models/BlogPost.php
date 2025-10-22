<?php

namespace App\Models;

use Laravel\Scout\Searchable;


use Illuminate\Database\Eloquent\Model;
use App\Jobs\UpdateSearchIndex;


class BlogPost extends Model
{
    use Searchable;
    protected $fillable = ['title', 'body', 'tags', 'published_at'];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'tags' => $this->tags,
            'published_at' => $this->published_at,
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
