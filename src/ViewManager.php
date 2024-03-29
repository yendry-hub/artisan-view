<?php

namespace Sven\ArtisanView;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sven\ArtisanView\Exceptions\ViewAlreadyExists;

class ViewManager
{
    private function __construct(protected Config $config, protected Filesystem $filesystem)
    {
    }

    public static function make(Config $config, Filesystem $filesystem): self
    {
        return new self($config, $filesystem);
    }

    public function create(string $view): bool
    {
        $this->everyView($view, function ($file) {
            $this->filesystem->makeDirectory(
                $this->filesystem->dirname($file),
                0755,
                true,
                true
            );

            if ($this->filesystem->exists($file)) {
                throw new ViewAlreadyExists('A view already exists at "'.$file.'".');
            }

            $contents = BlockBuilder::make()->build($this->config);

            $this->filesystem->put($file, $contents);
        });

        return true;
    }

    public function delete(string $view): bool
    {
        $this->everyView($view, function ($file) {
            $this->filesystem->delete($file);

            $location = $this->filesystem->dirname($file);

            $files = $this->filesystem->files($location);
            $directories = $this->filesystem->directories($location);

            if ($files === [] && $directories === []) {
                $this->filesystem->deleteDirectory($location);
            }
        });

        return true;
    }

    protected function everyView(string $view, callable $callable): Collection
    {
        return Collection::make($this->getFileNames($view))
            ->map(function ($filename) {
                return $this->config->getLocation().DIRECTORY_SEPARATOR.$filename;
            })
            ->each($callable);
    }

    protected function getFileNames(string $view): array
    {
        $viewPaths = str_replace(
            '.',
            DIRECTORY_SEPARATOR,
            $this->getViewNames($view)
        );

        return array_map(function ($viewName) {
            return $viewName.$this->config->getExtension();
        }, $viewPaths);
    }

    protected function getViewNames(string $view): array
    {
        if (!$this->config->isResource()) {
            return Arr::wrap($view);
        }

        return array_map(function ($verb) use ($view) {
            return $view.'.'.$verb;
        }, $this->config->getVerbs());
    }
}
