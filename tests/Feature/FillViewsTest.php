<?php

namespace Sven\ArtisanView\Tests\Feature;

use Sven\ArtisanView\Commands\MakeView;
use Sven\ArtisanView\Tests\TestCase;
use Sven\LaravelViewAssertions\InteractsWithViews;

class FillViewsTest extends TestCase
{
    use InteractsWithViews;

    /** @test */
    public function it_adds_the_extends_tag_to_the_generated_view(): void
    {
        /** @var \Illuminate\Testing\PendingCommand $command */
        $command = $this->artisan(MakeView::class, [
            'name' => 'index',
            '--extends' => 'layouts.master',
        ]);

        $command->assertExitCode(0);
        unset($command);

        $this->assertViewExists('index');
        $this->assertViewEquals("@extends('layouts.master')".PHP_EOL.PHP_EOL, 'index');
    }

    /** @test */
    public function it_adds_a_section_to_the_generated_view(): void
    {
        /** @var \Illuminate\Testing\PendingCommand $command */
        $command = $this->artisan(MakeView::class, [
            'name' => 'index',
            '--section' => ['content'],
        ]);

        $command->assertExitCode(0);
        unset($command);

        $this->assertViewEquals("@section('content')".PHP_EOL.PHP_EOL.'@endsection'.PHP_EOL.PHP_EOL, 'index');
    }

    /** @test */
    public function it_adds_multiple_sections_to_the_generated_view(): void
    {
        /** @var \Illuminate\Testing\PendingCommand $command */
        $command = $this->artisan(MakeView::class, [
            'name' => 'index',
            '--section' => ['content', 'scripts'],
        ]);

        $command->assertExitCode(0);
        unset($command);

        $contentSection = "@section('content')".PHP_EOL.PHP_EOL.'@endsection'.PHP_EOL.PHP_EOL;
        $scriptSection = "@section('scripts')".PHP_EOL.PHP_EOL.'@endsection'.PHP_EOL.PHP_EOL;

        $this->assertViewEquals($contentSection.$scriptSection, 'index');
    }

    /** @test */
    public function it_adds_an_inline_section_to_the_generated_view(): void
    {
        /** @var \Illuminate\Testing\PendingCommand $command */
        $command = $this->artisan(MakeView::class, [
            'name' => 'index',
            '--section' => ['title:My Title'],
        ]);

        $command->assertExitCode(0);
        unset($command);

        $this->assertViewEquals("@section('title', 'My Title')".PHP_EOL.PHP_EOL, 'index');
    }

    /** @test */
    public function it_adds_a_normal_an_an_inline_section_to_the_generated_view(): void
    {
        /** @var \Illuminate\Testing\PendingCommand $command */
        $command = $this->artisan(MakeView::class, [
            'name' => 'index',
            '--section' => ['content', 'title:My Title'],
        ]);

        $command->assertExitCode(0);
        unset($command);

        $contentSection = "@section('content')".PHP_EOL.PHP_EOL.'@endsection'.PHP_EOL.PHP_EOL;

        $this->assertViewEquals($contentSection."@section('title', 'My Title')".PHP_EOL.PHP_EOL, 'index');
    }

    /** @test */
    public function it_adds_an_extends_and_section_block_to_the_view(): void
    {
        $this->artisan(MakeView::class, [
            'name' => 'index',
            '--extends' => 'layouts.master',
            '--section' => ['title:My Title', 'content'],
        ]);

        $expectedBlocks = [
            "@extends('layouts.master')".PHP_EOL.PHP_EOL,
            "@section('title', 'My Title')".PHP_EOL.PHP_EOL,
            "@section('content')".PHP_EOL.PHP_EOL.'@endsection'.PHP_EOL.PHP_EOL,
        ];

        $this->assertViewEquals(implode('', $expectedBlocks), 'index');
    }
}
