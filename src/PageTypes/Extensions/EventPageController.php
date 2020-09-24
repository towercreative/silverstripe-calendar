<?php
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
        if (isset($_GET['past'])) {
            return 'past';
        } else {
            return 'coming';
        }
    }
    public function Events()
    {
        if ($this->ComingOrPastEvents() == 'past') {
            //return $this->model->PastEvents();
            return $this->PastEvents();
        } else {
            return $this->ComingEvents();
        }
    }
}
