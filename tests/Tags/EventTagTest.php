<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Tags;

use SilverStripe\Dev\SapphireTest;

class EventTagTest extends SapphireTest
{
    protected static $fixture_file = 'tests/event-tags.yml';

    public function test_get_tag_url_segment(): void
    {
        $tag = $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag1');

        # This will have been converted during the save whilst saving the fixtures file
        // @todo The -1 does not look correc there
        $this->assertEquals('cumberland-sausages-1', $tag->Slug);
    }


    public function test_get_tag_url_segment_duplicate_title(): void
    {
        # Duplicate titles are saved with a segment of -1, -2 etc
        $this->assertEquals(
            'beans-1',
            $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag3')->Slug,
        );
        $this->assertEquals(
            'beans-2',
            $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag4')->Slug,
        );
        $this->assertEquals(
            'beans-3',
            $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag5')->Slug,
        );
    }
}
