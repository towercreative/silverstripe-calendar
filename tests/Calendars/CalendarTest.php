<?php

namespace TitleDK\Calendar\Tests\Calendars;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Calendars\Calendar;

class CalendarTest extends SapphireTest
{
    /** @var Calendar */
    private $calendar;

    public function setUp()
    {
        $this->calendar = new Calendar();
        return parent::setUp();
    }


    public function testCanView()
    {
        $this->assertTrue($this->calendar->canView());
    }

    public function testCanCreate()
    {
        $this->assertFalse($this->calendar->canCreate());
    }

    public function testCanEdit()
    {
        $this->assertFalse($this->calendar->canEdit());
    }

    public function testCanDelete()
    {
        $this->assertFalse($this->calendar->canDelete());
    }
}
