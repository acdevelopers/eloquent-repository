<?php

namespace AcDevelopers\EloquentRepository\Console;


use AcDevelopers\ArtisanGenerator\Console\BaseCommand;

/**
 * Class ProviderMakeCommand
 *
 * @package AcDevelopers\EloquentRepository\Console
 */
class ProviderMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ac:repository:provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository service provider.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/provider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('ac-developers.repository.generator.namespaces.providers', '\Providers');
    }
}
