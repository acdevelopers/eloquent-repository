<?php

namespace AcDevelopers\EloquentRepository\Console;

use AcDevelopers\ArtisanGenerator\Console\BaseCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class RepositoryMakeCommand
 *
 * @package AcDevelopers\EloquentRepository\Console
 */
class RepositoryMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ac:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if ($this->confirm('Would you like to bind this repository with it\'s interface in a service provider?', true)) {
            $this->call('ac:bind', [
                '--provider' => config('ac-developers.repository.generator.paths.provider', 'RepositoryServiceProvider'),
                '--concrete' => $this->parseClass($this->argument('name'),
                                    config('ac-developers.repository.generator.namespaces.repositories', '\Repositories')),
                '--interface' => $this->parseClass("{$this->argument('name')}Interface",
                                    config('ac-developers.repository.generator.namespaces.interfaces.repository', '\Contracts\Repositories'))
            ]);
        }
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        if (config('ac-developers.repository.generator.namespaces.repositories', '\Repositories')
            == config('ac-developers.repository.generator.namespaces.interfaces.repository', '\Contracts\Repositories')) {

            $replace["use DummyFullInterfaceClass;"] = '';
        }

        $replace = $this->buildInterfaceReplacements($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('ac-developers.repository.generator.namespaces.repositories', '\Repositories');
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildInterfaceReplacements(array $replace)
    {
        $interfaceName = $this->argument('name').'Interface';

        $interfaceClass = $this->parseClass($interfaceName,
            config('ac-developers.repository.generator.namespaces.interfaces.repository', '\Contracts\Repositories'));

        $params = ['name' => $interfaceName];

        $this->option('force') ? $params['--force'] = true : null;

        if (! class_exists($interfaceClass)) {
            $this->call('ac:repository:interface', $params);
        }

        return array_merge($replace, [
            'DummyFullInterfaceClass' => $interfaceClass,
            'DummyInterfaceClass' => class_basename($interfaceClass)
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Generate a resource controller for the given model.'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }
}
