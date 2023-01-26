<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Events;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\CsvBulkLoader;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Categories\EventCategory;

// @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint

/**
 * PlayerCsvBulkLoader
 *
 * @author Anselm Christophersen <ac@anselm.dk>
 * @date October 2015
 */
class EventCsvBulkLoader extends CsvBulkLoader
{

    /** @var array<string,string> */
    public $columnMap = [
        'Title' => 'Title',
        'Start Date' => '->importStartDate',
        'Start Time' => '->importStartTime',
        'End Date' => '->importEndDate',
        'End Time' => '->importEndTime',
        'Calendar' => 'Calendar.Title',
    ];

    /** @var array<string,array<string,string>> */
    public $relationCallbacks = [
        'Calendar.Title' => [
            'relationname' => 'Calendar',
            'callback' => 'getCalendarByTitle',
            ],
        ]
    ;

    /** @var string */
    private static $dateFormat = 'm/d/Y';

    /** @var string */
    private static $timeFormat = 'H:i';


    /** @return array<mixed> */
    public function getImportSpec(): array
    {
        $spec = [];
        $dateFormat = Config::inst()->get('EventCsvBulkLoader', 'dateFormat');

        /*
         * Fields
         */
        $spec['fields'] = [
            'Title' => \_t('Event.Title', 'Title'),
            'Start Date' => \_t(
                'Event.StartDateSpec',
                'Start date in format {dateformat}',
                '',
                ['dateformat' => $dateFormat],
            ),
            'Start Time' => \_t('Event.StartTime', 'Start Time'),
            'End Date' => \_t(
                'Event.EndDateSpec',
                'End date in format {dateformat}' . '',
                ['dateformat' => $dateFormat],
            ),
            'End Time' => \_t('Event.EndTime', 'End Time'),
        ];

        /*
         * Relations
         */
        $relations = [];
        if (Config::inst()->get(Calendar::class, 'enabled')) {
            $relations['Calendar'] = \_t('Event.CalendarTitle', 'Calendar title');
        }

        if (Config::inst()->get(EventCategory::class, 'enabled')) {
            $relations['Categories'] = \_t('Event.CategoryTitles', 'Category titles');
        }

        $spec['relations'] = $relations;

        return $spec;
    }


    public static function importStartDate(Event &$event, string $val): void
    {
        $dateTime = self::importDate($val);
        $event->TimeFrameType = 'DateTime';
        $event->StartDateTime = $dateTime;
        $event->AllDay = true;
    }


    /** @throws \Exceptionq */
    public static function importStartTime(Event &$event, string $val): void
    {
        if (!\strlen($val)) {
            return;
        }
        $dt = new \DateTime($event->StartDateTime);
        $date = $dt->format('Y-m-d');
        $event->StartDateTime = $date . ' ' . $val;
        $event->AllDay = false;
    }


    public static function importEndDate(Event &$event, string $val): void
    {
        $dateTime = self::importDate($val);
        $event->EndDateTime = $dateTime;
    }


    /** @throws \Exception */
    public static function importEndTime(Event &$event, string $val): void
    {
        if (!\strlen($val)) {
            return;
        }
        $dt = new \DateTime($event->EndDateTime);
        $date = $dt->format('Y-m-d');
        $event->EndDateTime = $date . ' ' . $val;
    }


    /** @throws \SilverStripe\ORM\ValidationException */
    public static function findOrCreateCalendarByTitle(string $title): DataObject
    {
        $c = Calendar::get()->filter('Title', $title)->First();
        if ($c && $c->exists()) {
            return $c;
        }

        $c = new Calendar();
        $c->Title = $title;
        $c->write();

        return $c;
    }


    /** @return string|\TitleDK\Calendar\Events\DateTime */
    protected static function importDate(string $dateAsString, string $rt = 'string')
    {
        $dateFormat = Config::inst()->get(EventCsvBulkLoader::class, 'dateFormat');
        $dateFormat .= ' ' . 'H:i';
        //$val = $val . '0:00';
        $dateTime = \date_create_from_format($dateFormat, $dateAsString);

        return $rt === 'string'
            ? $dateTime->format('Y-m-d H:i:s')
            : $dateTime;
    }
}
