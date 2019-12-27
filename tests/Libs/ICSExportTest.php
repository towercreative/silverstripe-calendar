<?php

namespace TitleDK\Calendar\Tests\Libs;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Libs\ICSExport;

class ICSExportTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var Calendar */
    private $calendar;

    public function setUp()
    {
        parent::setUp();
        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar');
    }

    public function test__construct()
    {
        $export = new ICSExport([$this->calendar]);
    }

    public function testGetFile()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testIcs_date()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGenerateEventString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCleanString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testIcs_from_sscal()
    {
        $this->markTestSkipped('TODO');
    }

    public function testInit()
    {
        $this->markTestSkipped('TODO');
    }

    public function testIndex()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCal()
    {
        $this->markTestSkipped('TODO');
    }

    public function testAll()
    {
        $this->markTestSkipped('TODO');
    }

    public function testMy()
    {
        $this->markTestSkipped('TODO');
    }

    public function testMemberCalendar()
    {
        $this->markTestSkipped('TODO');
    }

    public function testOutput()
    {
        $this->markTestSkipped('TODO');
    }
}
