<?php
namespace TitleDK\Calendar\SubSites;

/**
 * Class \TitleDK\Calendar\SubSites\CalendarSubsiteExtension
 *
 * @property \TitleDK\Calendar\SubSites\CalendarSubsiteExtension $owner
 * @property int $SubsiteID
 * @method \Subsite Subsite()
 */
class CalendarSubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );
}
