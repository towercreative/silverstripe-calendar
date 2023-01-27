<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Tags;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Tags\EventTag;

class EventTagTest extends SapphireTest
{
    protected static $fixture_file = '../event-tags.yml';

    public function testGetTagUrlSegment(): void
    {
        $tag = $this->objFromFixture(EventTag::class, 'tag1');

        # This will have been converted during the save whilst saving the fixtures file
        // @todo The -1 does not look correc there
        $this->assertEquals('cumberland-sausages-1', $tag->Slug);
    }


    public function testGetTagUrlSegmentDuplicateTitle(): void
    {
        # Duplicate titles are saved with a segment of -1, -2 etc
        $this->assertEquals(
            'beans-1',
            $this->objFromFixture(EventTag::class, 'tag3')->Slug
        );
        $this->assertEquals(
            'beans-2',
            $this->objFromFixture(EventTag::class, 'tag4')->Slug
        );
        $this->assertEquals(
            'beans-3',
            $this->objFromFixture(EventTag::class, 'tag5')->Slug
        );
    }
}
