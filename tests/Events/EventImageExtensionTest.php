<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Events;

use SilverStripe\Assets\Image;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventImageExtensionTest extends SapphireTest
{
    protected static $fixture_file = ['../events.yml'];

    /**
     * Assert there is a field called FeaturedImage, which is what the extension adds
     */
    public function testUpdateCMSFields(): void
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $fields = $event->getCMSFields();
        $rootTab = $fields->fieldByName('Root');
        /** @var \TitleDK\Calendar\Tests\Events\Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();
        $imageField = $fields->fieldByName('FeaturedImage');
        $this->assertNotNull($imageField);
    }


    public function testGetThumbnailNoImage(): void
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $event->setField('FeaturedImage', null);
        $this->assertEquals('(No Image)', $event->getThumbnail());
    }

    // @todo Not sure how to write a unit test involving asset Files

    public function testGetThumbnailAnEmptyFileImage(): void
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');

        /** @var \SilverStripe\Assets\Image $image */
        $image = Image::create();
        $image->write();
        $event->FeaturedImageID = $image->ID;

        // there is no file associated with the image, as such the rendering will be null
        $this->assertNull($event->getThumbnail());
    }
}
