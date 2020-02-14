<?php

namespace App\Console\Code\Base;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class BaseMakeCommand extends GeneratorCommand
{
    protected $file_name;
    protected $generation_namespace;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->file_name) {
            throw new \Exception('Name not Found!');
        }

        $this->type = $this->option('domain') . class_basename($this->argument('name')) . $this->getSuffix();

        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('extend') ?
            __DIR__ . '/../stubs/extended-' . $this->file_name . '.stub' :
            __DIR__ . '/../stubs/' . $this->file_name . '.stub';
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['extend', 'e', InputOption::VALUE_NONE, 'Generate Extended ' . \Str::plural(ucfirst($this->file_name))],
            ['domain', 'd', InputOption::VALUE_NONE, 'Generate' . \Str::plural(ucfirst($this->file_name)) . ' for Specific Domain'],
            ['index', 'i', InputOption::VALUE_NONE, 'is Index' . ucfirst($this->file_name) . ' ?'],
        ];
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('DummyClass', $this->classNameHandler($class), $stub);
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        $name = $this->getNamespace($name) . '\\' . $this->classNameHandler($class);

        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function classNameHandler($class)
    {
        return $this->option('domain') . $class . $this->getSuffix();
    }

    protected function getSuffix()
    {
        return $this->option('index') ? 'Index' . ucfirst($this->file_name) : ucfirst($this->file_name);
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $chain = $this->replaceNamespace($stub, $name);
        if ($this->option('extend')) {
            $chain->replaceExtendClass($stub, $this->option('extend'));
        }
        return $chain->replaceClass($stub, $name);
    }
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . $this->generation_namespace . $this->option('domain');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param $name
     * @return string
     */
    protected function replaceExtendClass(&$stub, $name)
    {
        $name = str_replace('/', '\\', $name);

        $namespaceClass = trim($this->rootNamespace(), '\\') . $this->generation_namespace . $name;

        $class = class_basename($namespaceClass);

        $stub = str_replace(
            [
                'NamespacedDummyExtendedClass',
                'DummyExtendedClass',
            ],
            [
                $namespaceClass,
                $class,
            ],
            $stub
        );

        return $this;
    }
}
