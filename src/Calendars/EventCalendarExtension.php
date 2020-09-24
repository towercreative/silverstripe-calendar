<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Calendars;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Event Calendar Extension
 * Allowing events to have calendars
 *
 * @package calendar
 * @subpackage calendars
 * @property \TitleDK\Calendar\Calendars\EventCalendarExtension $owner
 * @property int $CalendarID
 * @method \TitleDK\Calendar\Calendars\Calendar Calendar()
 */
class EventCalendarExtension extends DataExtension
{
    private static $has_one = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar';
    private );

    public function updateFrontEndFields(FieldList $fields): void
    {
        $calendarDropdown = DropdownField::create(
            'CalendarID',
            'Calendar',
            Calendar::get()->map('ID', 'Title'),
        )
                ->setEmptyString('Choose calendar...');

        $fields->push($calendarDropdown);
    }
}
