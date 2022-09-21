<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use TitleDK\Calendar\Events\Event;

// @phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Event Registration Form
 * *
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistrationForm extends Form
{

    /**
     * Contructor
     */
    public function __construct(Controller $controller, string $name)
    {
        //Fields
        $fields = EventRegistration::singleton()->getFrontEndFields();

        //Actions
        $actions = FieldList::create(
            FormAction::create("doRegister")
                ->setTitle("Register")
        );

        //Validator
        $validator = RequiredFields::create(
            [
                'Name',
                Email::class,
            ],
        );
        $this->addExtraClass('EventRegistrationForm');
        $this->addExtraClass($name);

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }


    public function setDone(): void
    {
        $this->setFields(
            FieldList::create(
                LiteralField::create(
                    'CompleteMsg',
                    "We've received your registration.",
                ),
            ),
        );
        $this->setActions(FieldList::create());
    }


    /**
     * Register action
     */
    public function doRegister(array $data, Form $form)
    {
        $r = new EventRegistration();
        $form->saveInto($r);

        $EventDetails = Event::get()->byID($r->EventID);

        if ($EventDetails->TicketsRequired) {
            $r->AmountPaid = ($r->AmountPaid/100);
        }
        $r->write();

        $from = Email::config()->get('admin_email');
        $to = $r->Email;
        $bcc = $EventDetails->RSVPEmail;
        $subject = "Event Registration - ".$EventDetails->Title." - ".\date("d/m/Y H:ia");
        $body = "";

        $email = new Email($from, $to, $subject, $body, null, null, $bcc);
        $email->setTemplate('EventRegistration');
        $email->send();

        // @todo why is this here?
        //  exit;
    }


    /** @param string|int|float|bool $value */
    public function setFormField(string $name, $value): void
    {
        $fields = $this->Fields();
        foreach ($fields as $field) {
            if ($field->Name !== $name) {
                continue;
            }

            $field->setValue($value);
        }
    }
}
