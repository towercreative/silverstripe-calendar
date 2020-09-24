<?php declare(strict_types = 1);

namespace TitleDK\Calendar\PageTypes;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * Class \TitleDK\Calendar\PageTypes\EventPage_Controller
 *
 * @property \TitleDK\Calendar\PageTypes\EventPage dataRecord
 * @method \TitleDK\Calendar\PageTypes\EventPage data()
 * @mixin \TitleDK\Calendar\PageTypes\EventPage
 */
class EventPageController extends \PageController
{

    /** @return string past if the parameter is 'past', otherwise 'coming' */
    public function ComingOrPastEvents(): string
    {
        $past = $this->getRequest()->getVar('past');

        return isset($past)
            ? 'past'
            : 'coming';
    }


    public function Events(): DataList
    {
        if ($this->ComingOrPastEvents() === 'past') {
            return $this->PastEvents();
        }

        return $this->ComingEvents();
    }
}
