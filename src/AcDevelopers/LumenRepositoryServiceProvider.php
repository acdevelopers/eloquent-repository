<?php
namespace AcDevelopers\EloquentRepository;

use Illuminate\Support\ServiceProvider;

/**
 * Class LumenRepositoryServiceProvider
 *
 * @package AcDevelopers\EloquentRepository
 */
class LumenRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('AcDevelopers\EloquentRepository\Console\RepositoryMakeCommand');
        $this->app->register('AcDevelopers\EloquentRepository\EventServiceProvider');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
