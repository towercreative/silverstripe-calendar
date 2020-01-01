<?php

namespace TitleDK\Calendar\Tests\PageTypes;

use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\PageTypes\EventPage;

class EventPageTest extends SapphireTest
{
    use DateTimeHelperTrait;

    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var EventPage */
    private $eventPage;

    public function setUp()
    {
        parent::setUp();
        $this->eventPage = $this->objFromFixture(EventPage::class, 'eventpage1');
        $midDecember = $this->carbonDateTime('2019-12-15 14:00:00');
        Carbon::setTestNow($midDecember);
    }


    public function testComingEvents()
    {
        $comingEvents = $this->eventPage->ComingEvents()->sort('StartDateTime');
        foreach ($comingEvents as $event) {
            $carbonStartDateTime = $this->carbonDateTime($event->StartDateTime);
            $this->assertGreaterThan(Carbon::now()->timestamp, $carbonStartDateTime->timestamp);
        }

        // checked events.yml file manually for number of events after Dec 15
        $this->assertEquals(4, $comingEvents->count());
    }

    public function testPastEvents()
    {
        $pastEvents = $this->eventPage->PastEvents()->sort('StartDateTime');
        foreach ($pastEvents as $event) {
            $carbonStartDateTime = $this->carbonDateTime($event->StartDateTime);
            $this->assertLessThan(Carbon::now()->timestamp, $carbonStartDateTime->timestamp);
        }

        // checked events.yml file manually for number of events after Dec 15
        $this->assertEquals(5, $pastEvents->count());
    }

    public function testGetCMSFields()
    {
        $fields = $this->eventPage->getCMSFields();

        /** @var TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // This is present for PostgresSQL on Travis only
        $fields->removeByName('InstallWarningHeader');

        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields->toArray());
        $this->assertEquals(['Title', 'URLSegment', 'MenuTitle', 'Content', 'Metadata'], $names);
    }

    public function testGetCalendarTitle()
    {
        // this uses the title of the page
        $this->assertEquals($this->eventPage->Title, $this->eventPage->getCalendarTitle());
    }
}
