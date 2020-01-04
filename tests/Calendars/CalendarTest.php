<?php

namespace TitleDK\Calendar\Tests\Calendars;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;

class CalendarTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml'];

    /** @var Calendar */
    private $calendar;

    /** @var Member */
    private $member;



    public function setUp()
    {
        parent::setUp();

        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $this->member = $this->objFromFixture(Member::class, 'member1');
        $this->logInAs($this->member);
    }

    public function test_get_cms_fields()
    {
        // this will contain the root tab
        $fields = $this->calendar->getCMSFields();

        /** @var TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList()->toArray();

        $titles = array_map(function($field) {
            return $field->Name;
        }, $fields);

        $this->assertEquals(['Slug', 'Title', 'Color', 'Groups'], $titles);
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
