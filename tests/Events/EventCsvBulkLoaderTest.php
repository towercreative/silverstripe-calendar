<?php

namespace TitleDK\Calendar\Tests\Events;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Events\EventCsvBulkLoader;

class EventCsvBulkLoaderTest extends SapphireTest
{
    /** @var EventCsvBulkLoader */
    private $bulkLoader;

    /** @var Event */
    private $event;

    public function setUp()
    {
        parent::setUp();
        $this->bulkLoader = new EventCsvBulkLoader('');
        $this->event = new Event();
        $this->event->Title = 'test event';
        $this->event->write();
    }

    public function test_get_import_spec()
    {
        $spec = $this->bulkLoader->getImportSpec();
        $this->assertEquals([
            'Title' => 'Title',
            'Start Date' => 'Start date in format ',
            'Start Time' => 'Start time',
            'End Date' => 'End date in format ',
            'End Time' => 'End time'
        ], $spec['fields']);

        $this->assertEquals([
            'Calendar' => 'Calendar title',
            'Categories' => 'Category titles'
        ], $spec['relations']);
    }

    public function test_import_start_date()
    {
        EventCsvBulkLoader::importStartDate($this->event, '2019-12-15 08:30', null);
        $this->assertEquals('2019-12-15 08:30:00', $this->event->StartDateTime);
        $this->assertEquals('DateTime', $this->event->TimeFrameType);
        $this->assertTrue( $this->event->AllDay);
    }

    function testImportStartTime()
    {
        $this->markTestSkipped('TODO');
    }

    public function testImportEndDate()
    {
        EventCsvBulkLoader::importStartDate($this->event, '2019-12-15 07:30', null);
        EventCsvBulkLoader::importEndTime($this->event, '2019-12-15 08:30', null);
        $this->assertEquals('2019-12-15 07:30:00', $this->event->StartDateTime);
        $this->assertEquals('2019-12-15 08:30:00', $this->event->EndDateTime);
        $this->assertEquals('DateTime', $this->event->TimeFrameType);
        $this->assertTrue( $this->event->AllDay);
    }

    public function testImportEndTime()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFindOrCreateCalendarByTitle()
    {
        $this->markTestSkipped('TODO');
    }
}
