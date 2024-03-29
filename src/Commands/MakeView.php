<?php

namespace Sven\ArtisanView\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Sven\ArtisanView\Config;
use Sven\ArtisanView\ViewManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeView extends Command
{
    use Concerns\ChoosesViewLocation;

    protected $name = 'make:view';

    protected $description = 'Create a new view';

    public function __construct(protected Repository $config)
    {
        parent::__construct();
    }

    public function handle(Filesystem $filesystem): int
    {
        $manager = ViewManager::make($this->config(), $filesystem);

        $manager->create($this->argument('name'));

        return 0;
    }

    private function config(): Config
    {
        return Config::make()
            ->setExtension($this->option('extension'))
            ->setResource($this->option('resource'), $this->option('verb'))
            ->setExtends($this->option('extends'))
            ->setSections($this->option('section'))
            ->setLocation($this->path());
    }

    protected function pathQuestion(): string
    {
        return 'Where should the view be created?';
    }

    protected function possibleLocations(): array
    {
        return $this->config->get('view.paths', []);
    }

    protected function exceptionMessage(): string
    {
        return 'There are no paths configured to store the view(s) in.';
    }

    protected function getOptions(): array
    {
        return [
            ['extension', null, InputOption::VALUE_OPTIONAL, 'The extension of the generated view.', 'blade.php'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Whether or not a RESTful resource should be created.'],
            ['verb', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The HTTP verb(s) to generate views for.', ['index', 'show', 'create', 'edit']],
            ['section', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The "@section" tags to pre-fill in the created view(s).'],
            ['extends', null, InputOption::VALUE_OPTIONAL, 'The view to "@extend" from the created view(s).'],
            ['with-yields', null, InputOption::VALUE_NONE, 'Whether or not to add all "@yield" sections from extended template (if "--extends" was provided)'],
            ['with-stacks', null, InputOption::VALUE_NONE, 'Whether or not to add all "@stacks" from extended template as @push (if "--extends" was provided)'],
        ];
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the view to create.'],
        ];
    }
}
