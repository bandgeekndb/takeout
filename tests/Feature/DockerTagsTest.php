<?php

namespace Tests\Feature;

use App\Services\MySql;
use App\Shell\DockerTags;
use GuzzleHttp\Client;
use Mockery as M;
use Tests\TestCase;

class DockerTagsTest extends TestCase
{
    /** @test */
    function it_lists_10_newest_available_tags_for_service()
    {
        $mysql = app(MySql::class);
        $dockerTags = app(DockerTags::class, ['service' => $mysql]);
        $tags = $dockerTags->getTags();

        $this->assertEquals('latest', $tags[0]);
        $this->assertTrue($tags->contains('5.7'));
        $this->assertCount(10, $tags);
    }

    /** @test */
    function it_gets_the_latest_tag_not_named_latest()
    {
        $mysql = app(MySql::class);

        $dockerTags = M::mock(DockerTags::class, [app(Client::class), $mysql])
            ->makePartial();

        $tags = collect(['latest', 'next latest tag yay']);
        $dockerTags->shouldReceive('getTags')->andReturn($tags);

        $tag = $dockerTags->getLatestTag();

        $this->assertEquals('next latest tag yay', $tag);
    }
}
