<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

/**
 * Event Registration
 *
 * @package calendar
 * @subpackage registrations
 * @property string $RegistrationEmbargoAt
 * @property string $Name
 * @property string $PayersName
 * @property string $Email
 * @property string $Status
 * @property int $NumberOfTickets
 * @property string $AmountPaid
 * @property string $Notes
 * @property int $EventID
 * @method \TitleDK\Calendar\Events\Event Event()
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Registrations\Attendee> Attendees()
 * @mixin \TitleDK\Calendar\Registrations\AttendeesExtension
 * @mixin \TitleDK\Calendar\Events\EventRegistrationEmbargoExtension
 */
class EventRegistration extends DataObject
{
    public const EVENT_REGISTRATION_SUCCESS_SESSION_KEY = 'event_registration_successful';

    public const EVENT_REGISTRATION_KEY = 'event_registration_id';

    private static $table_name = 'EventRegistration';
    private static $singular_name = 'Registration';
    private static $plural_name = 'Registrations';

    private static $db = [
        'Name' => 'Varchar',
        'PayersName' => 'Varchar',
        'Email' => 'Varchar',
        'Status' => "Enum('Available,Unpaid,AwaitingPayment,PaymentExpired,Paid,Cancelled,Booked','Available')",
        'NumberOfTickets' => 'Int',
        'AmountPaid' => 'Money',
        'Notes' => 'HTMLText',
    ];

    private static $has_one = [
        'Event' => 'TitleDK\Calendar\Events\Event',
    ];

    private static $default_sort = 'Created DESC';

    private static $defaults = [
        'Status' => 'Available',
    ];

    private static $summary_fields = [
        'Created' => 'Created DESC',
    'PayersName' => 'Name (Payer)',
    'AttendeeName' => 'Name (Attendee)',
    'Status' => 'Payment Status',
    'NumberOfTickets' => 'Tickets',
    'AmountPaid' => 'Amount Paid',
    'RegistrationCode' => 'Registration Code',
    ];

    /**
     * Frontend fields
     */
    public function getFrontEndFields($params = null): FieldList
    {
        $fields = FieldList::create(
            TextField::create('Name'),
            TextField::create('Email'),
            HiddenField::create('EventID'),
        );

        $this->extend('updateFrontEndFields', $fields);

        return $fields;
    }


    public function getRegistrationCode(): string
    {
        return \strtoupper($this->event()->Slug) . '-' . \str_pad((string) $this->ID, 4, "0", \STR_PAD_LEFT);
    }
}
