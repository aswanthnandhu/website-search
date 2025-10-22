<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\Page;
use App\Models\Faq;
use Illuminate\Support\Facades\Log;

class RebuildSearchIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelClass;

    public function __construct(string $modelClass = null)
    {
        $this->modelClass = $modelClass;
    }

    public function handle()
    {
        $models = $this->modelClass ? [$this->modelClass] : [
            BlogPost::class,
            Product::class,
            Page::class,
            Faq::class,
        ];

        foreach ($models as $modelClass) {
            $this->rebuildModelIndex($modelClass);
        }
    }

    protected function rebuildModelIndex(string $modelClass)
    {
        try {
            Log::info("Starting search index rebuild for {$modelClass}");

            $modelClass::chunk(100, function ($records) use ($modelClass) {
                foreach ($records as $record) {
                    $record->searchable();
                }
            });

            Log::info("Completed search index rebuild for {$modelClass}");
        } catch (\Exception $e) {
            Log::error("Failed to rebuild search index for {$modelClass}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error('Search index rebuild failed permanently', [
            'model_class' => $this->modelClass,
            'error' => $exception->getMessage(),
        ]);
    }
}