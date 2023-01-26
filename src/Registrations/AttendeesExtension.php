<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataExtension;

/**
 * Extend event registration
 *
 * Each registration can have multiple attendees, for example an IT company register 4 people for an event such as a
 * conference or seminar
 *
 * Class EventRegistrationAttendeesExtension
 *
 * @package TitleDK\Calendar\Attendee
 * @property \TitleDK\Calendar\Registrations\EventRegistration|\TitleDK\Calendar\Registrations\AttendeesExtension $owner
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Registrations\Attendee> Attendees()
 */
class AttendeesExtension extends DataExtension
{
    private static $belongs_many_many = [
        'Attendees' => Attendee::class,
    ];

    // @todo This will need fixed
    private static $summary_fields = ['Title', 'AttendeeName', 'FirstName', 'Surname', 'Phone', 'Email'];


    public function updateCMSFields(FieldList $fields): void
    {

        $gridField = GridField::create(
            'Attendees',
            'Attendees',
            $this->owner->Attendees(),
        );

        //, 'NumberOfTickets' );
        $fields->addFieldToTab('Root.Attendees', $gridField);
    }
}
