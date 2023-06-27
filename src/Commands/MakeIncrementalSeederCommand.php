<?php

namespace Karl456\IncrementalSeeders\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class MakeIncrementalSeederCommand extends SeederMakeCommand
{
    use ConfirmableTrait;

    protected $name = 'make:incremental-seeder';

    protected $description = 'Seed the database with records';

    public function __construct(Filesystem $files)
    {
        $this->time = now()->format('Y_m_d_His');

        parent::__construct($files);
    }

    public function handle()
    {
        parent::handle();
    }

    public function replaceClass($stub, $name)
    {
        $name = $name.'_'.$this->time;

        return parent::replaceClass($stub, $name);
    }

    protected function resolveStubPath($stub)
    {
        return __DIR__.'/../..'.$stub;
    }

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/incremental-seeder.stub');
    }

    public function getPath($name)
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

        return $this->laravel->basePath(config('incremental-seeders.path')).DIRECTORY_SEPARATOR.$name.'_'.$this->time.'.php';
    }

    protected function rootNamespace()
    {
        return 'Database\IncrementalSeeders\\';
    }
}
