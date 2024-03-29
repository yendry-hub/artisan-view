<?php

namespace Sven\ArtisanView\Blocks;

use Illuminate\Support\Str;

class InlineSection implements Block
{
    protected string $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function applicable(): bool
    {
        return Str::contains($this->contents, ':');
    }

    public function render(): string
    {
        [$name, $content] = explode(':', $this->contents);

        return "@section('$name', '$content')";
    }
}
