<?php

namespace App\Console\Code\Commands;

use Illuminate\Console\Command;

class ServicesGenerateCommand extends Command
{
    protected $baseDomain = 'Base';
    const DOMAINS = ['Admin'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:gen:services {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Services For Domains';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->generateBaseClasses($name);

        $this->generateDomainClasses($name, self::DOMAINS);
    }


    protected function getExtendedClassName($name, $suffix)
    {
        $name = str_replace('/', '\\', $name);
        $name = $this->getNamespace($name) . '\\' . $this->baseDomain . class_basename($name);
        return $this->baseDomain . '/' . trim($name, '\\') . $suffix;
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function generateBaseClasses($name)
    {
        // create base service
        $this->call('code:make:service', [
            'name' => $name,
            '--domain' => $this->baseDomain,
        ]);
    }

    protected function generateDomainClasses(string $name, array $domains)
    {
        foreach ($domains as $domain) {
            // create service resource
            $this->call('code:make:service', [
                'name' => $name,
                '--domain' => $domain,
                '--extend' => $this->getExtendedClassName($name, 'Service')
            ]);
        }
    }
}
