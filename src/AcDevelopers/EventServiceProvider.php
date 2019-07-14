<?php

namespace AcDevelopers\EloquentRepository;

use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package AcDevelopers\EloquentRepository
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'AcDevelopers\EloquentRepository\Events\RepositoryEntityCreated' => [
            'AcDevelopers\EloquentRepository\Listeners\CleanCacheRepository'
        ],
        'AcDevelopers\EloquentRepository\Events\RepositoryEntityUpdated' => [
            'AcDevelopers\EloquentRepository\Listeners\CleanCacheRepository'
        ],
        'AcDevelopers\EloquentRepository\Events\RepositoryEntityDeleted' => [
            'AcDevelopers\EloquentRepository\Listeners\CleanCacheRepository'
        ]
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        $events = app('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
