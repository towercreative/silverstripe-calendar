<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\ORM\DataObject;

/**
 * Add a location to an event
 *
 * @todo Use Mappable
 * @package calendar
 * @property string $Title
 * @property string $FirstName
 * @property string $Surname
 * @property string $Company
 * @property string $Phone
 * @property string $Email
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Registrations\EventRegistration> Registrations()
 */
class Attendee extends DataObject
{
    /** @var string */
    private static $table_name = 'Attendee';

    /** @var array<string,string> */
    private static $db = [
        'Title' => 'Varchar(255)',
        'FirstName' => 'Varchar(255)',
        'Surname' => 'Varchar(255)',
        'Company' => 'Varchar(255)',
        'Phone' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
    ];

    /** @var array<string> */
    private static $summary_fields = [
      'Title', 'FirstName', 'Surname', 'Company', 'Phone', 'Email',
    ];

    /** @var array<string,string> */
    private static $many_many = [
        'Registrations' => EventRegistration::class,
    ];
}
