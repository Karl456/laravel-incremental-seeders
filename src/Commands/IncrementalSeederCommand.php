<?php

namespace Karl456\IncrementalSeeders\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Karl456\IncrementalSeeders\Models\IncrementalSeeder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class IncrementalSeederCommand extends Command
{
    use ConfirmableTrait;

    protected $name = 'db:incremental-seed';

    protected $description = 'Seed the database with records';

    public function __construct(
        protected Filesystem $files,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $path = $this->laravel->basePath(config('incremental-seeders.path'));

        if (! $this->files->exists($path)) {
            throw new DirectoryNotFoundException('"'.config('incremental-seeders.path').'" directory was not found.');
        }

        $ranSeeders = IncrementalSeeder::query()->get();

        $seeders = Collection::make($path)
            ->flatMap(function ($path) {
                return str_ends_with($path, '.php') ? [$path] : $this->files->glob($path.'/*_*.php');
            })->filter()->values()->keyBy(function ($file) {
                return Str::of($file)->basename()->replace('.php', '')->match('/\d{4}_\d{2}_\d{2}_\d{6}/');
            })->reject(function ($file) use ($ranSeeders) {
                return $ranSeeders->where('seeder', Str::of($file)->basename()->replace('.php', '')->toString())->first();
            })->sortBy(function ($file, $key) {
                return $key;
            })->all();

        if (! $seeders) {
            $this->info('Nothing to seed.');
            return;
        }

        $this->info('Running seeders.');

        foreach ($seeders as $key => $file) {
            $this->files->requireOnce($file);

            $seederName = Str::of($file)->basename('.php')->toString();
            $seederClass = 'Database\\IncrementalSeeders\\'.$seederName;
            $seeder = new $seederClass;

            if (method_exists($seeder, 'run')) {
                $seeder->run();
                $this->info($seederName . ' run.');
            }

            IncrementalSeeder::query()->create([
                'seeder' => $seederName,
            ]);
        }

        $this->info('All seeders run.');
    }
}
