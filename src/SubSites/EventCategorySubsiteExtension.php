<?php
namespace TitleDK\Calendar\SubSites;

/**
 * Class \TitleDK\Calendar\SubSites\EventCategorySubsiteExtension
 *
 * @property \TitleDK\Calendar\SubSites\EventCategorySubsiteExtension $owner
 * @property int $SubsiteID
 * @method \Subsite Subsite()
 */
class EventCategorySubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );
}
