<?php

namespace App\Console\Code\Commands;

use App\Console\Code\Base\BaseMakeCommand;


class RepositoryMakeCommand extends BaseMakeCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'code:make:repo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repo';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repostory';


    protected $file_name = 'Repository';
    protected $generation_namespace = '\Repositories';

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
        return $this->replaceBody($stub, $name)->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    public function replaceBody(&$stub, $name)
    {
        $stub = str_replace(
            ['dummyModel', 'DummyModel'],
            [\Str::snake(class_basename($name)), class_basename($name)],
            $stub
        );
        return $this;
    }
}
