<?php

namespace AcDevelopers\EloquentRepository\Console;

use AcDevelopers\ArtisanGenerator\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ServiceInterfaceMakeCommand
 *
 * @package AcDevelopers\EloquentRepository\Console
 */
class ServiceInterfaceMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ac:service:interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service interface class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service Interface';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/service.interface.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('ac-developers.repository.generator.namespaces.interfaces.service', '\Contracts\Services');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }
}
