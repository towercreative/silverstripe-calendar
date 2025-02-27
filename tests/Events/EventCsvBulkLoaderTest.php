<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Events;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Events\EventCsvBulkLoader;

class EventCsvBulkLoaderTest extends SapphireTest
{
    protected static $fixture_file = '../events.yml';

    /** @var \TitleDK\Calendar\Events\EventCsvBulkLoader */
    private $bulkLoader;

    /** @var \TitleDK\Calendar\Events\Event */
    private $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->bulkLoader = EventCsvBulkLoader::create('');
        $this->event = Event::create();
        $this->event->Title = 'test event';
        $this->event->write();
    }


    public function testGetImportSpec(): void
    {
        $spec = $this->bulkLoader->getImportSpec();
        $this->assertEquals([
            'Title' => 'Title',
            'Start Date' => 'Start date in format ',
            'Start Time' => 'Start time',
            'End Date' => 'End date in format ',
            'End Time' => 'End time',
        ], $spec['fields']);

        $this->assertEquals([
            'Calendar' => 'Calendar title',
            'Categories' => 'Category titles',
        ], $spec['relations']);
    }


    public function testImportStartDate(): void
    {
        EventCsvBulkLoader::importStartDate($this->event, '2019-12-15 08:30', null);
        $this->assertEquals('2019-12-15 08:30:00', $this->event->StartDateTime);
        $this->assertEquals('DateTime', $this->event->TimeFrameType);
        $this->assertTrue($this->event->AllDay);
    }


    public function testImportStartTime(): void
    {
        EventCsvBulkLoader::importStartDate($this->event, '2019-12-15 21:30', null);
        $this->assertEquals('2019-12-15 21:30:00', $this->event->StartDateTime);

        EventCsvBulkLoader::importStartTime($this->event, '08:30', null);

        // @todo Minor inconsistency here
        $this->assertEquals('2019-12-15 08:30', $this->event->StartDateTime);
    }


    public function testEmptyImportStartTime(): void
    {
        $this->assertNull(EventCsvBulkLoader::importStartTime($this->event, '', null));
    }


    public function testImportEndDate(): void
    {
        EventCsvBulkLoader::importEndDate($this->event, '2019-12-15 08:30', null);
        $this->assertEquals('2019-12-15 08:30:00', $this->event->EndDateTime);
        $this->assertNull($this->event->TimeFrameType);
        $this->assertTrue($this->event->AllDay);
    }


    public function testImportEndTime(): void
    {
        EventCsvBulkLoader::importStartDate($this->event, '2019-12-15 21:30', null);
        EventCsvBulkLoader::importEndDate($this->event, '2019-12-15 23:30', null);
        $this->assertEquals('2019-12-15 21:30:00', $this->event->StartDateTime);
        $this->assertEquals('2019-12-15 23:30:00', $this->event->EndDateTime);

        EventCsvBulkLoader::importEndTime($this->event, '22:45', null);

        // @todo Minor inconsistency here
        $this->assertEquals('2019-12-15 22:45', $this->event->EndDateTime);
    }


    public function testEmptyImportEndTime(): void
    {
        $this->assertNull(EventCsvBulkLoader::importEndTime($this->event, '', null));
    }


    public function testFindOrCreateCalendarByTitle(): void
    {
        $calendar1 = EventCsvBulkLoader::findOrCreateCalendarByTitle($this->event, 'Duty Roster', null);
        $this->assertNotNull($calendar1);
        $this->assertEquals('TitleDK\Calendar\Calendars\Calendar', \get_class($calendar1));
        $calendar2 = EventCsvBulkLoader::findOrCreateCalendarByTitle($this->event, 'Duty Roster', null);
        $this->assertNotNull($calendar2);
        $this->assertEquals('TitleDK\Calendar\Calendars\Calendar', \get_class($calendar2));
        $this->assertEquals($calendar1->ID, $calendar2->ID);
    }
}
