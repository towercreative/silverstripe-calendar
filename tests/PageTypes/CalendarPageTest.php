<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\PageTypes;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CalendarPageTest extends SapphireTest
{
    public function testIsCalendar(): void
    {
        $calendarPage = CalendarPage::create();
        $this->assertTrue($calendarPage->isCalendar());
    }


    public function testGetCMSFields(): void
    {
        $calendar = Calendar::create();
        $calendar->write();
        $calendarPage = CalendarPage::create();
        $calendarPage->Calendars()->add($calendar);
        $fields = $calendarPage->getCMSFields();
        /** @var \SilverStripe\Forms\TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var \SilverStripe\Forms\Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // This is present for PostgresSQL on Travis only
        $fields->removeByName('InstallWarningHeader');

        $names = \array_map(
            static function ($field) {
                return $field->Name;
            },
            $fields->toArray(),
        );
        $this->assertEquals([
            'Title',
            'URLSegment',
            'MenuTitle',
            'Calendars',
            'Content',
            'Metadata',
        ], $names);
    }
}
