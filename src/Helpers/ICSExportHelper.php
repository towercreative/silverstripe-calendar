<?php
namespace TitleDK\Calendar\Helpers;

use Carbon\Carbon;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;
use Ramsey\Uuid\Uuid;
use SilverStripe\ORM\DataObject;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Libs\ICSExport;


class ICSExportHelper
{
    use DateTimeHelperTrait;

    /**
     * @var Calendar The SS Calendar object
     */
    protected $ssCalendar;

    /**
     * @var string The ICS output
     */
    protected $strics = '';

    /**
     * @param $ssCalendar SilverStripe calendar
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    public function processCalendar($ssCalendar)
    {
        $this->ssCalendar = $ssCalendar;
        $this->strics = '';
        $icsCalendar = new \Jsvrcek\ICS\Model\Calendar();

        // @todo Fix this, config I guess
        $icsCalendar->setProdId('-//My Company//Cool CalNendar App//EN');

        // @todo Check ordering
        /** @var Event $ssEvent */
        foreach ($ssCalendar->Events() as $ssEvent) {
            $icsEvent = new CalendarEvent();

            $startCarbon = $this->carbonDateTime($ssEvent->StartDateTime);
            $endCarbon = $this->carbonDateTime($ssEvent->EndDateTime);

            // this is the genuinely random UUID - base this on something else instead?
            // see https://packagist.org/packages/ramsey/uuid
            $uuid = Uuid::uuid4();

            $icsEvent->setStart($startCarbon->toDateTime())
                ->setEnd($endCarbon->toDateTime())
                ->setSummary($ssEvent->DetailsSummary())
                ->setAllDay($ssEvent->AllDay)
                ->setUid($uuid->toString())
                ->setStatus('CONFIRMED')
            ;

            $icsCalendar->addEvent($icsEvent);
        }

        $exporter = new CalendarExport(new CalendarStream(), new Formatter());
        $exporter->addCalendar($icsCalendar);

        $this->strics = $exporter->getStream();
        return $this->strics;
    }

    /**
     * getFile
     *
     * @param string $strFilename the names of the file
     */
    public function getFile($strFilename)
    {
        ob_start();
        header("Content-type: text/calendar");
        header('Content-Disposition: attachment; filename="' .  $strFilename . '"');
        echo $this->strics;
        ob_flush();
        die();
    }

    /**
     * getString
     *
     * @return string ics string
     */
    public function getString()
    {
        return $this->strics;
    }

}
