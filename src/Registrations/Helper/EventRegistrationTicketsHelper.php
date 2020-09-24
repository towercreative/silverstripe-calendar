<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations\Helper;

use TitleDK\Calendar\Events\Event;

class EventRegistrationTicketsHelper
{
    /** @var \TitleDK\Calendar\Registrations\Helper\Event */
    protected $event;

    /**
     * EventRegistrationTicketsHelper constructor.
     *
     * @param Event $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }


    /**
     * Ascertain the number of tickets remaining
     * @return int the number of tickets remaining
     */
    public function numberOfTicketsRemaining()
    {
        //$sql = "SELECT SUM('NumberOfTickets')";
        $used = $this->numberOfTicketsNotAvailable();

        return $this->event->NumberOfAvailableTickets - $used;
    }


    /**
     * Get the number of tickets freely available (ie not being processed)
     *
     * @param $registrations
     */
    public function numberOfTicketsNotAvailable(): int
    {
        $nTickets = 0;
        $registrations = $this->event->Registrations();
        foreach ($registrations as $reg) {
            if ($reg->Status === 'Available') {
                continue;
            }

            $nTickets += $reg->NumberOfTickets;
        }

        return $nTickets;
    }
}
