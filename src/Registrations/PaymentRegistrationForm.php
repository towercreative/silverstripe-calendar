<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

// @phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Event Registration Form
 *
 * @package calendar
 * @subpackage registrations
 */
class PaymentRegistrationForm extends Form
{

    /**
     * Contructor
     */
    public function __construct(type $controller, type $name)
    {
        //Fields
        $fields = FieldList::create(
            TextField::create('Name', 'Name'),
            TextField::create('PayersName', "Payer's Name"),
            EmailField::create('Email', 'Email'),
            NumericField::create('NumberOfTickets', 'Number of Tickets'),
            TextareaField::create("Notes"),
            HiddenField::create('EventID')
        );

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
            ]
        );

        $this->addExtraClass('PaymentRegistrationForm');
        $this->addExtraClass($name);

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }


    public function setDone(): void
    {
        $this->setFields(
            FieldList::create(
                LiteralField::create(
                    'CompleteMsg',
                    "We've received your registration."
                )
            )
        );
        $this->setActions(FieldList::create());
    }


    /**
     * @param array<string,string|int|float|bool> $data
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function doRegister(array $data, Form $form): \SS_HTTPResponse
    {
        $registration = new EventRegistration();
        $form->saveInto($registration);
        $registration->write();

        return "Thanks. We've received your registration, with payment!!";
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
