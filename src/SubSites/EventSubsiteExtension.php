<?php
namespace TitleDK\Calendar\SubSites;

/**
 * Class \TitleDK\Calendar\SubSites\EventSubsiteExtension
 *
 * @property \TitleDK\Calendar\SubSites\EventSubsiteExtension $owner
 * @property int $SubsiteID
 * @method \Subsite Subsite()
 */
class EventSubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );
}
