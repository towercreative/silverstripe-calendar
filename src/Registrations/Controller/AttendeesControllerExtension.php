<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations\Controller;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;

// @phpcs:disable Generic.Files.LineLength.TooLong
// @phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
/**
 * Extend event registration controller
 *
 * Class EventRegistrationAttendeesExtension
 *
 * @package TitleDK\Calendar\Attendee
 * @property \TitleDK\Calendar\Registrations\EventRegistrationController|\TitleDK\Calendar\Registrations\Controller\AttendeesControllerExtension $owner
 */
class AttendeesControllerExtension extends Extension
{

    public function updateEventRegistrationForm(Form $form): void
    {
        //Requirements::javascript('silverstripe/admin:thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript('titledk/silverstripe-calendar:thirdparty/parsley/parsley.min.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/registration/Attendees.js');
        $fields = $form->Fields();

        $attendeesField = LiteralField::create(
            'AttendeesHTML',
            '<div id="attendees-list">Attendees will appear here</div>'
        );
        $fields->insertBefore('NumberOfTickets', $attendeesField);

        $addAttendeeButtonHTML = '<a href="#" id="add-attendee-button">Add Attendee</a>';
        $fields->insertBefore('NumberOfTickets', LiteralField::create('AddAttendee', $addAttendeeButtonHTML));

        $fields->fieldByName('NumberOfTickets')->setReadonly(true);

        $jsonField = HiddenField::create('AttendeesJSON');
        $data = $form->getData();
        if (!isset($data['AttendeesJSON'])) {
            $member = Security::getCurrentUser();
            if ($member) {
                $details = [
                    [
                        'first_name' => $member->FirstName,
                        'surname' => $member->Surname,
                        'phone' => !isset($member->Phone) ? '' : $member->Phone,
                        'email' => $member->Email,
                        'company' => !isset($member->Company) ? '' : $member->CompanyName,
                        'title' => !isset($member->Title) ? '' : $member->Title,
                    ],
                ];

                $jsonField->setValue(\json_encode($details));
            } else {
                $jsonField->setValue('[]');
            }
        }

        $fields->push($jsonField);
        $form->setFields($fields);
    }
}
