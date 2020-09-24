<?php declare(strict_types = 1);

namespace TitleDK\Calendar\PageTypes;

/**
 * Class \TitleDK\Calendar\PageTypes\EventPage_Controller
 *
 * @property \TitleDK\Calendar\PageTypes\EventPage dataRecord
 * @method \TitleDK\Calendar\PageTypes\EventPage data()
 * @mixin \TitleDK\Calendar\PageTypes\EventPage
 */
class EventPage_Controller extends \PageController
{

    public function ComingOrPastEvents()
    {
        return isset($_GET['past'])
            ? 'past'
            : 'coming';
    }


    public function Events()
    {
        if ($this->ComingOrPastEvents() === 'past') {
            //return $this->model->PastEvents();
            return $this->PastEvents();
        }

        return $this->ComingEvents();
    }
}
