<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Calendars;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;

class CalendarTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml'];

    /** @var \TitleDK\Calendar\Calendars\Calendar */
    private $calendar;

    /** @var \SilverStripe\Security\Member */
    private $member;



    public function setUp(): void
    {
        parent::setUp();

        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $this->member = $this->objFromFixture(Member::class, 'member1');
        $this->logInAs($this->member);
    }


    public function testGetCMSFields(): void
    {
        // this will contain the root tab
        $fields = $this->calendar->getCMSFields();

        /** @var \TitleDK\Calendar\Tests\Calendars\TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var \TitleDK\Calendar\Tests\Calendars\Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList()->toArray();

        $titles = \array_map(static function ($field) {
            return $field->Name;
        }, $fields);

        $this->assertEquals(['Slug', 'Title', 'Color', 'Groups'], $titles);
    }


    public function testCanView(): void
    {
        $this->assertTrue($this->calendar->canView());
    }


    public function testCanCreate(): void
    {
        $this->assertFalse($this->calendar->canCreate());
    }


    public function testCanEdit(): void
    {
        $this->assertFalse($this->calendar->canEdit());
    }


    public function testCanDelete(): void
    {
        $this->assertFalse($this->calendar->canDelete());
    }
}
