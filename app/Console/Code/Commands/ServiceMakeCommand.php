<?php

namespace App\Console\Code\Commands;

use App\Console\Code\Base\BaseMakeCommand;


class ServiceMakeCommand extends BaseMakeCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'code:make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';


    protected $file_name = 'service';
    protected $generation_namespace = '\Services\\';
}
