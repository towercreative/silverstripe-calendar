<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;

/**
 * Event Calendar Extension
 * Allowing events to have calendars
 *
 * @package calendar
 * @subpackage calendars
 * @property \TitleDK\Calendar\Calendars\GroupsCalendarExtension $owner
 * @method \SilverStripe\ORM\ManyManyList|\TitleDK\Calendar\Calendars\Calendar[] Calendar()
 */
class GroupsCalendarExtension extends DataExtension
{
    private static $many_many = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar'
    );
}
