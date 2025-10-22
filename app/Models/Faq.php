<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;
class Faq extends Model
{
    use Searchable;
    protected $table = 'faqs';
    protected $fillable = ['question', 'answer'];
    public function toSearchableArray()
    {
        return [
            'question' => $this->question,
            'answer' => $this->answer,
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
