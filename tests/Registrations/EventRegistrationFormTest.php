<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\EventRegistrationController;
use TitleDK\Calendar\Registrations\EventRegistrationForm;

class EventRegistrationFormTest extends SapphireTest
{
    public function testConstruct(): void
    {
        $form = EventRegistrationForm::create(EventRegistrationController::create(), 'eventFormTest');
        $fields = $form->Fields()->toArray();
        $names = \array_map(static function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals(['Name', 'Email', 'EventID', 'SecurityID'], $names);
    }


    public function testSetDone(): void
    {
        $form = EventRegistrationForm::create(EventRegistrationController::create(), 'eventFormTest');
        $form->setDone();
        $fields = $form->Fields()->toArray();
        $names = \array_map(static function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals(['CompleteMsg', 'SecurityID'], $names);
    }


    public function testDoRegister(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testSetFormField(): void
    {
        $form = EventRegistrationForm::create(EventRegistrationController::create(), 'eventFormTest');
        $form->setFormField('Email', 'fred@fred.com');

        /** @var \SilverStripe\Forms\FieldList $fields */
        $fields = $form->Fields();
        $emailField = $fields->fieldByName('Email');
        $this->assertEquals('fred@fred.com', $emailField->Value());
    }
}
