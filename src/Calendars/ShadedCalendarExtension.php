<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBBoolean;

/**
 * Shaded Calendar Extension
 * Allowing calendars to be shaded
 * This can be used with calendars containing secondary information
 *
 * @package calendar
 * @subpackage calendars
 * @property \TitleDK\Calendar\Calendars\ShadedCalendarExtension $owner
 * @property boolean $Shaded
 */
class ShadedCalendarExtension extends DataExtension
{

    private static $db = array(
        'Shaded' => DBBoolean::class,
    );
}
