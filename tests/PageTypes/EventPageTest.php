<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\PageTypes;

use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\PageTypes\EventPage;

class EventPageTest extends SapphireTest
{

    use DateTimeHelper;

    protected static $fixture_file = ['../events.yml', '../eventpages.yml'];

    /** @var \TitleDK\Calendar\PageTypes\EventPage */
    private $eventPage;

    public function setUp(): void
    {
        parent::setUp();

        $this->eventPage = $this->objFromFixture(EventPage::class, 'eventpage1');
        $midDecember = $this->carbonDateTime('2019-12-15 14:00:00');
        Carbon::setTestNow($midDecember);
    }


    public function testComingEvents(): void
    {
        $comingEvents = $this->eventPage->ComingEvents()->sort('StartDateTime');
        foreach ($comingEvents as $event) {
            $carbonStartDateTime = $this->carbonDateTime($event->StartDateTime);
            $this->assertGreaterThan(Carbon::now()->timestamp, $carbonStartDateTime->timestamp);
        }

        // checked events.yml file manually for number of events after Dec 15
        $this->assertEquals(4, $comingEvents->count());
    }


    public function testPastEvents(): void
    {
        $pastEvents = $this->eventPage->PastEvents()->sort('StartDateTime');
        foreach ($pastEvents as $event) {
            $carbonStartDateTime = $this->carbonDateTime($event->StartDateTime);
            $this->assertLessThan(Carbon::now()->timestamp, $carbonStartDateTime->timestamp);
        }

        // checked events.yml file manually for number of events after Dec 15
        $this->assertEquals(5, $pastEvents->count());
    }


    public function testGetCMSFields(): void
    {
        $fields = $this->eventPage->getCMSFields();

        /** @var \SilverStripe\Forms\TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var \SilverStripe\Forms\Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // This is present for PostgresSQL on Travis only
        $fields->removeByName('InstallWarningHeader');

        $names = \array_map(static function ($field) {
            return $field->Name;
        }, $fields->toArray());
        $this->assertEquals(['Title', 'URLSegment', 'MenuTitle', 'Content', 'Metadata'], $names);
    }


    public function testGetCalendarTitle(): void
    {
        // this uses the title of the page
        $this->assertEquals($this->eventPage->Title, $this->eventPage->getCalendarTitle());
    }
}
