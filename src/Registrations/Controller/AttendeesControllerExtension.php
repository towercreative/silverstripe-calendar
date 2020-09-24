<?php
namespace TitleDK\Calendar\Registrations\Controller;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

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

    public function updateEventRegistrationForm(Form $form)
    {
        //Requirements::javascript('silverstripe/admin:thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript('titledk/silverstripe-calendar:thirdparty/parsley/parsley.min.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/registration/Attendees.js');
        $fields = $form->Fields();

        $attendeesField = LiteralField::create('AttendeesHTML', '<div id="attendees-list">Attendees will appear here</div>');
        $fields->insertBefore('NumberOfTickets', $attendeesField);

        $addAttendeeButtonHTML = '<a href="#" id="add-attendee-button">Add Attendee</a>';
        $fields->insertBefore('NumberOfTickets', LiteralField::create('AddAttendee', $addAttendeeButtonHTML));

        $fields->fieldByName('NumberOfTickets')->setReadonly(true);

        $jsonField = HiddenField::create('AttendeesJSON');
        $data = $form->getData();
        if (!isset($data['AttendeesJSON'])) {
            if ($member = Security::getCurrentUser()) {
                $details = [
                    [
                        'first_name' => $member->FirstName,
                        'surname' => $member->Surname,
                        'phone' => empty($member->Phone)  ? '' : $member->Phone,
                        'email' => $member->Email,
                        'company' => empty($member->Company) ? '' : $member->CompanyName,
                        'title' => empty($member->Title) ? '' : $member->Title
                    ]
                ];

                $jsonField->setValue(json_encode($details));
            } else {
                $jsonField->setValue('[]');
            }
        }

        $fields->push($jsonField);
        $form->setFields($fields);
    }
}
