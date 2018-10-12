<?php

namespace Sven\ArtisanView\Tests\Unit;

use Sven\ArtisanView\Config;
use Sven\ArtisanView\Tests\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_sets_and_gets_the_extension()
    {
        $config = Config::make()->setExtension('html.twig');

        $this->assertEquals('html.twig', $config->getExtension());
    }

    /** @test */
    public function it_sets_and_gets_the_resource_and_verbs()
    {
        $config = Config::make()->setResource(true, ['foo', 'bar', 'baz']);

        $this->assertTrue($config->isResource());
        $this->assertEquals(['foo', 'bar', 'baz'], $config->getVerbs());
    }

    /** @test */
    public function it_sets_and_gets_the_force_value()
    {
        $config = Config::make()->setForce(true);

        $this->assertTrue($config->isForce());
    }

    /** @test */
    public function it_sets_and_gets_the_location()
    {
        $config = Config::make()->setLocation('some/path/to/create/the/view/in');

        $this->assertEquals('some/path/to/create/the/view/in', $config->getLocation());
    }
}
