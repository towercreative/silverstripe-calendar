<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Calendars;

use SilverStripe\ORM\DataExtension;

/**
 * Event Calendar Extension
 * Allowing events to have calendars
 *
 * @package calendar
 * @subpackage calendars
 * @property \TitleDK\Calendar\Calendars\GroupsCalendarExtension $owner
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Calendars\Calendar> Calendar()
 */
class GroupsCalendarExtension extends DataExtension
{
    private static $many_many = [
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar'
    ];
}
