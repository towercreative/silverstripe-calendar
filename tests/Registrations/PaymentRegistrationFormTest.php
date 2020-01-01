<?php

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\PaymentRegistrationForm;

class PaymentRegistrationFormTest extends SapphireTest
{
    public function test__construct()
    {
        $form = new PaymentRegistrationForm(null, 'paymentFormTest');
        $fields = $form->Fields()->toArray();
        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals([
            'Name',
            'PayersName',
            'Email',
            'NumberOfTickets',
            'Notes',
            'EventID',
            'SecurityID'
        ], $names);
    }

    public function testSetDone()
    {
        $form = new PaymentRegistrationForm(null, 'paymentFormTest');
        $form->setDone();
        $fields = $form->Fields()->toArray();
        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals([
            'CompleteMsg',
            'SecurityID'
        ], $names);
    }

    public function testDoRegister()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSetFormField()
    {
        $form = new PaymentRegistrationForm(null, 'eventFormTest');
        $form->setFormField('Email', 'fred@fred.com');

        /** @var FieldList $fields */
        $fields = $form->Fields();
        $emailField = $fields->fieldByName('Email');
        $this->assertEquals('fred@fred.com', $emailField->Value());
    }
}
