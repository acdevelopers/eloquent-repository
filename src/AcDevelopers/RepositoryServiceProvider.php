<?php
namespace AcDevelopers\EloquentRepository;

use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 *
 * @package AcDevelopers\EloquentRepository
 */
class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/repository.php' => config_path('ac-developers/repository.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../config/repository.php', 'repository');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'repository');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register commands.
        if ($this->app->runningInConsole()) {
            $this->commands([
                'AcDevelopers\EloquentRepository\Console\BindingCommand',
                'AcDevelopers\EloquentRepository\Console\CriteriaMakeCommand',
                'AcDevelopers\EloquentRepository\Console\InterfaceMakeCommand',
                'AcDevelopers\EloquentRepository\Console\ProviderMakeCommand',
                'AcDevelopers\EloquentRepository\Console\RepositoryMakeCommand',
                'AcDevelopers\EloquentRepository\Console\ServiceMakeCommand',
                'AcDevelopers\EloquentRepository\Console\ServiceInterfaceMakeCommand'
            ]);
        }

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
