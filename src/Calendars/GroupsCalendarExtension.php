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
 * @method DataList Calendars Get the calendars associated with the group
 *
 * @package    calendar
 * @subpackage calendars
 */
class GroupsCalendarExtension extends DataExtension
{
    private static $many_many = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar'
    );
}
