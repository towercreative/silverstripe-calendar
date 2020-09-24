<?php

namespace TitleDK\Calendar\Tests\PageTypes;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CalendarPageTest extends SapphireTest
{
    public function testIsCalendar()
    {
        $calendarPage = new CalendarPage();
        $this->assertTrue($calendarPage->isCalendar());
    }

    public function testGetCMSFields()
    {
        $calendar = new Calendar();
        $calendar->write();
        $calendarPage = new CalendarPage();
        $calendarPage->Calendars()->add($calendar);
        $fields = $calendarPage->getCMSFields();
        /** @var TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // This is present for PostgresSQL on Travis only
        $fields->removeByName('InstallWarningHeader');

        $names = array_map(function ($field) {
            return $field->Name;
        },
            $fields->toArray());
        $this->assertEquals([
            'Title',
            'URLSegment',
            'MenuTitle',
            'Calendars',
            'Content',
            'Metadata'
        ], $names);
    }
}
