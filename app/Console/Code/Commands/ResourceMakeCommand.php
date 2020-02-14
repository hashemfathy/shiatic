<?php

namespace App\Console\Code\Commands;

use App\Console\Code\Base\BaseMakeCommand;


class ResourceMakeCommand extends BaseMakeCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'code:make:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';


    protected $file_name = 'resource';
    protected $generation_namespace = '\Http\Resources\\';
}
