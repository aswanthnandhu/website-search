<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class SearchLog extends Model
{
    use Searchable;
    protected $fillable = ['query', 'user_id'];

    public function toSearchableArray(): array
    {
        return [
            'query' => $this->query,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
