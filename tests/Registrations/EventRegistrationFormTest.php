<?php

namespace TitleDK\Calendar\Tests\Registrations;

use \SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use TitleDK\Calendar\Registrations\EventRegistrationForm;

class EventRegistrationFormTest extends SapphireTest
{
    public function test__construct()
    {
        $form = new EventRegistrationForm(null, 'eventFormTest');
        $fields = $form->Fields()->toArray();
        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals(['Name', 'Email', 'EventID', 'SecurityID'], $names);
    }

    public function testSetDone()
    {
        $form = new EventRegistrationForm(null, 'eventFormTest');
        $form->setDone();
        $fields = $form->Fields()->toArray();
        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals(['CompleteMsg', 'SecurityID'], $names);
    }

    public function testDoRegister()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSetFormField()
    {
        $form = new EventRegistrationForm(null, 'eventFormTest');
        $form->setFormField('Email', 'fred@fred.com');

        /** @var FieldList $fields */
        $fields = $form->Fields();
        $emailField = $fields->fieldByName('Email');
        $this->assertEquals('fred@fred.com', $emailField->Value());
    }
}
