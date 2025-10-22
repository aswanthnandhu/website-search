<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class UpdateSearchIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $action;

    /**
     * Create a new job instance.
     *
     * @param Model $model
     * @param string $action 'create', 'update', or 'delete'
     */
    public function __construct(Model $model, string $action)
    {
        $this->model = $model;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            switch ($this->action) {
                case 'create':
                case 'update':
                    $this->model->searchable();
                    break;
                case 'delete':
                    $this->model->unsearchable();
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Search index update failed: ' . $e->getMessage(), [
                'model' => get_class($this->model),
                'model_id' => $this->model->id,
                'action' => $this->action,
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        Log::error('Search index job failed permanently', [
            'model' => get_class($this->model),
            'model_id' => $this->model->id,
            'action' => $this->action,
            'error' => $exception->getMessage(),
        ]);
    }
}