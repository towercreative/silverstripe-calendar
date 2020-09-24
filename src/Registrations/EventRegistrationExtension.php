<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\MoneyField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBInt;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\Registrations\Helper\EventRegistrationTicketsHelper;

/**
 * Allowing events to have registrations
 *
 * Add this extension to Event
 *
 * @package calendar
 * @subpackage registrations
 * @property \TitleDK\Calendar\Events\Event|\TitleDK\Calendar\Registrations\EventRegistrationExtension $owner
 * @property bool $Registerable
 * @property string $Cost
 * @property bool $TicketsRequired
 * @property int $NumberOfAvailableTickets
 * @property bool $PaymentRequired
 * @property string $RSVPEmail
 * @method \SilverStripe\ORM\DataList|array<\TitleDK\Calendar\Registrations\EventRegistration> Registrations()
 */
class EventRegistrationExtension extends DataExtension
{

    private static $db = array(
        'Registerable' => DBBoolean::class;
    private 'Cost' => 'Money';
    private 'TicketsRequired' => DBBoolean::class;
    private 'NumberOfAvailableTickets' => DBInt::class;
    private 'PaymentRequired' => DBBoolean::class ;
    private 'RSVPEmail' => 'Varchar(255)'
    );

    private static $has_many = array(
        'Registrations' => 'TitleDK\Calendar\Registrations\EventRegistration'
    );


    /**
     * Add CMS editing fields
     */
    public function updateCMSFields(FieldList $fields): void
    {
        $list = $this->getExportableRegistrationsList();
        $numberOfRegistrations = $this->owner->Registrations()->count();

        $exportButton = new GridFieldExportButton('buttons-before-left');
        $exportButton->setExportColumns($this->getExportFields());

        $fieldConfig = GridFieldConfig_RecordEditor::create($numberOfRegistrations)
            ->addComponent($exportButton)
            ->removeComponentsByType(GridFieldFilterHeader::class)
            ->addComponents(
                new GridFieldPrintButton('buttons-before-left'),
            );

        $listField = GridField::create(
            'Registrations',
            'Registrations',
            $list,
            $fieldConfig,
        );

        $listField->setModelClass(EventRegistration::class);

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header1', 'Event Registration', 2),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new CheckboxField('Registerable', 'Event Registration Required'),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header2', 'Who should the website send registration notifications to?', 4),
        );
        $fields->addFieldToTab(
            'Root.Registrations',
            new EmailField('RSVPEmail', 'RSVP Email'),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header3', 'Event Details', 2),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $ticketsRequiredField = new CheckboxField('TicketsRequired', 'Tickets Required'),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $nTicketsAvailableField = new NumericField(
                'NumberOfAvailableTickets',
                'Total Number of Available Tickets prior to Sale',
            ),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $paymentRequiredField = new CheckboxField(
                'PaymentRequired',
                'Payment Required (must also check "Tickets Required" for this to work)',
            ),
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $eventCostsHeader = new HeaderField('Header4', 'Event Costs (if payment required)', 2),
        );

        $mf = new MoneyField('Cost');

        //TODO this should be configurable
        $mf->setAllowedCurrencies(array('USD'));

        $fields->addFieldToTab(
            'Root.Registrations',
            $mf,
        );

        // show hide logic
        $nTicketsAvailableField->displayIf('TicketsRequired')->isChecked();
        $paymentRequiredField->displayIf('TicketsRequired')->isChecked();
        // does not work $mf->displayIf('TicketsRequired')->isChecked();
        $eventCostsHeader->displayIf('TicketsRequired')->isChecked();

        $helper = new EventRegistrationTicketsHelper($this->owner);

        $title = "Registrations (Unticketed)";
        if ($this->owner->TicketsRequired) {
            $ticketsRemaining = $helper->numberOfTicketsRemaining();
            $title = "Registrations (" . $ticketsRemaining . ' tickets remaining)';
        }

        $fields->addFieldToTab('Root.Registrations', $listField);
    }


    /**
     * Getter for registration link
     */
    public function getRegisterLink()
    {
        //$link = $o->getInternalLink() . "/register";
        //return $link;

        $detailStr = 'register/' . $this->owner->ID;

        $calendarPage = CalendarPage::get()->First();

        return $calendarPage->Link() . $detailStr;
    }


    public function RegistrationForm()
    {
        $eventRegistrationController = new EventRegistrationController();

        $form = $eventRegistrationController->registerform();

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);

        return $form;
    }


    public function RegistrationPaymentForm()
    {
        $eventRegistrationController = new EventRegistrationController();

        $form = $eventRegistrationController->paymentregisterform();

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);

        // @todo This is loading old data
        // $data = Controller::curr()->getRequest()->getSession()->get("FormData.{$form->getName()}.data");
        // return $data ? $form->loadDataFrom($data) : $form;

        return $form;
    }


    /**
     * Due to attendees being one to many, the list needs manipulated in memory (for now) to allow for the excel
     * export
     *
     * @todo individual tickets?
     * @return mixed
     */
    public function getExportableRegistrationsList()
    {
        $registrations = $this->owner->Registrations()->sort('Created');
        $updatedRecords = new ArrayList();
        foreach ($registrations as $record) {
            $attendees = $record->Attendees();
            // these are many many
            foreach ($attendees as $attendee) {
                $clonedRecord = clone $record;
                //$attendee->Title;
                $clonedRecord->Title = 'TITLE';
                $clonedRecord->FirstName = $attendee->FirstName;
                $clonedRecord->Surname = $attendee->Surname;
                $clonedRecord->AttendeeName = $attendee->FirstName . ' ' . $attendee->Surname;
                $clonedRecord->CompanyName = $attendee->Company;
                $clonedRecord->Phone = $attendee->Phone;
                $clonedRecord->Email = $attendee->Email;
                $updatedRecords->push($clonedRecord);
            }

            $registration = EventRegistration::get()->byID($record->ID);
            $record->RegistrationCode = $registration->getRegistrationCode();
        }

        return $updatedRecords;
    }


    public function getExportFields()
    {
        return ['RegistrationCode', 'Status', 'PayersName', 'FirstName', 'Surname', 'AttendeeName', 'CompanyName',
            'Phone', 'Email'];
    }


    /**
     * Sanitise a model class' name for inclusion in a link
     */
    protected function sanitiseClassName(string $class): string
    {
        return \str_replace('\\', '-', $class);
    }
}
