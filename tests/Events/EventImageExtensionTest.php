<?php

namespace TitleDK\Calendar\Tests\Events;

use SilverStripe\Assets\Image;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventImageExtensionTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml'];

    /**
     * Assert there is a field called FeaturedImage, which is what the extension adds
     */
    public function testUpdateCMSFields()
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $fields = $event->getCMSFields();
        $rootTab = $fields->fieldByName('Root');
        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();
        $imageField = $fields->fieldByName('FeaturedImage');
        $this->assertNotNull($imageField);
    }

    public function test_get_thumbnail_no_image()
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $event->setField('FeaturedImage', null);
        $this->assertEquals('(No Image)', $event->getThumbnail());
    }

    // @todo Not sure how to write a unit test involving asset Files

    public function test_get_thumbnail_an_empty_file_image()
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');

        /** @var Image $image */
        $image = new Image();
        $image->write();
        $event->FeaturedImageID = $image->ID;

        // there is no file associated with the image, as such the rendering will be null
        $this->assertNull( $event->getThumbnail());
    }
}
