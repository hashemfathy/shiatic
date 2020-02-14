<?php

namespace App\Console\Code\Commands;

use Illuminate\Console\Command;

class ResourcesGenerateCommand extends Command
{
    protected $baseDomain = 'Base';
    const DOMAINS = ['Admin'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:gen:resources {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Resources For Domains';

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
        // create simple index resource
        $this->call('code:make:resource', [
            'name' => $name,
            '--domain' => $this->baseDomain,
            '--index' => true
        ]);

        // create extended resource
        $this->call('code:make:resource', [
            'name' => $name,
            '--domain' => $this->baseDomain,
            '--extend' => $this->getExtendedClassName($name, 'IndexResource')
        ]);
    }

    protected function generateDomainClasses(string $name, array $domains)
    {
        foreach ($domains as $domain) {
            // create extended index resource
            $this->call('code:make:resource', [
                'name' => $name,
                '--domain' => $domain,
                '--extend' => $this->getExtendedClassName($name, 'IndexResource'),
                '--index' => true
            ]);

            // create extended resource
            $this->call('code:make:resource', [
                'name' => $name,
                '--domain' => $domain,
                '--extend' => $this->getExtendedClassName($name, 'Resource')
            ]);
        }
    }
}
