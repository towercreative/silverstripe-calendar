<?php
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
 * @method \SilverStripe\ORM\ManyManyList|\TitleDK\Calendar\Registrations\EventRegistration[] Registrations()
 */
class Attendee extends DataObject
{
    private static $table_name = 'Attendee';

    private static $db = [
        'Title' => 'Varchar(255)',
        'FirstName' => 'Varchar(255)',
        'Surname' => 'Varchar(255)',
        'Company' => 'Varchar(255)',
        'Phone' => 'Varchar(255)',
        'Email' => 'Varchar(255)'
    ];

    private static $summary_fields = [
      'Title', 'FirstName', 'Surname', 'Company', 'Phone', 'Email'
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Registrations' => EventRegistration::class
    ];
}
