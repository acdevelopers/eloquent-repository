<?php

namespace AcDevelopers\EloquentRepository\Console;


use AcDevelopers\ArtisanGenerator\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ServiceMakeCommand
 *
 * @package AcDevelopers\EloquentRepository\Console
 */
class ServiceMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ac:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository service.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/service.stub';
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

        if ($this->confirm('Would you like to bind this service with it\'s interface in a service provider?', true)) {
            $this->call('ac:bind', [
                '--provider' => config('ac-developers.repository.generator.paths.provider', 'RepositoryServiceProvider'),
                '--concrete' => $this->parseClass($this->argument('name'), config('ac-developers.repository.generator.namespaces.services', '\Services')),
                '--interface' => $this->parseClass("{$this->argument('name')}Interface",
                                    config('ac-developers.repository.generator.namespaces.interfaces.service', '\Contracts\Services'))
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

        $replace = $this->buildRepositoryReplacements($replace);

        if (config('ac-developers.repository.generator.namespaces.services', '\Services')
            == config('ac-developers.repository.generator.namespaces.interfaces.service', '\Contracts\Services')) {

            $replace["use DummyFullInterfaceClass;"] = '';
        }

        $replace = $this->buildInterfaceReplacements($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the repository replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRepositoryReplacements(array $replace)
    {
        $repositoryName  = $this->option('repository');

        $repositoryClass = $this->parseClass($repositoryName,
            config('ac-developers.repository.generator.namespaces.repositories', '\Repositories'));

        $params = ['name' => $repositoryName];

        $this->option('force') ? $params['--force'] = true : null;
        $params['--model'] = $this->option('model');

        if (! class_exists($repositoryClass)) {
            $this->call('ac:repository', $params);
        }

        if ($this->option('interface')) {
            $repositoryClass = $this->parseClass($this->option('repository').'Interface',
                config('ac-developers.repository.generator.namespaces.interfaces.repository', '\Contracts\Repositories'));
        }

        return array_merge($replace, [
            'DummyFullRepositoryClass' => $repositoryClass,
            'DummyRepositoryClass' => class_basename($repositoryClass)
        ]);
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
            config('ac-developers.repository.generator.namespaces.interfaces.service', '\Contracts\Services'));

        $params = ['name' => $interfaceName];

        $this->option('force') ? $params['--force'] = true : null;

        if (! class_exists($interfaceClass)) {
            $this->call('ac:service:interface', $params);
        }

        return array_merge($replace, [
            'DummyFullServiceInterfaceClass' => $interfaceClass,
            'DummyServiceInterfaceClass' => class_basename($interfaceClass)
        ]);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('ac-developers.repository.generator.namespaces.services', '\Services');
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
            ['repository', 'r', InputOption::VALUE_REQUIRED, 'Indicate a repository for the given service.'],
            ['interface', 'i', InputOption::VALUE_NONE, 'Indicated if you would like to use the interface of the given repository with this service.'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }
}
