<?php

namespace TitleDK\Calendar\Tests\Tags;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventTagTest extends SapphireTest
{
    protected static $fixture_file = 'tests/event-tags.yml';

    public function test_get_tag_url_segment()
    {
        $tag = $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag1');

        # This will have been converted during the save whilst saving the fixtures file
        $this->assertEquals('cumberland-sausages', $tag->URLSegment);
    }

    public function test_get_tag_url_segment_duplicate_title()
    {
        # Duplicate titles are saved with a segment of -1, -2 etc
        $this->assertEquals(
            'beans-1',
            $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag4')->URLSegment
        );
        $this->assertEquals(
            'beans-2',
            $this->objFromFixture('TitleDK\Calendar\Tags\EventTag', 'tag5')->URLSegment
        );
    }
}
