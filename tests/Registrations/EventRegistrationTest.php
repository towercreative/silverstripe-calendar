<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Registrations;

use Faker\Factory;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\EventRegistration;

class EventRegistrationTest extends SapphireTest
{
    protected static $fixture_file = 'tests/registered-events.yml';

    /** @var \TitleDK\Calendar\Events\Event */
    private $conference;

    public function setUp(): void
    {
        parent::setUp();

        $this->conference = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'conference');
    }


    public function testGetFrontEndFields(): void
    {
        $fields = $this->conference->getFrontEndFields()->toArray();
        $names = \array_map(static fn ($field) => $field->Name, $fields);
        $this->assertEquals(['Title', 'AllDay', 'StartDateTime', 'TimeFrameHeader', 'TimeFrameType', 'Clear', 'CalendarID'], $names);
    }


    public function test_get_registration_code(): void
    {
       // $this->generateFixtures();
        $registration = $this->objFromFixture(EventRegistration::class, 'registration10');
        $id = $registration->ID;
        $this->assertEquals('EXAMPLE-CONFERENCE-1-00' . $id, $registration->getRegistrationCode());
    }


    private function generateFixtures(): void
    {
        $attendees = '';
        /** @var \Faker\Factory $faker */
        $faker = Factory::create();
        for ($i=1; $i<=100; $i++) {
            $firstName = $faker->firstNameMale;
            $lastName = $faker->lastName;
            $email = $faker->email;
            $parts = \explode('@', $email);
            $email = \strtolower($firstName . '.' . $lastName . '@' . $parts[1]);
            $company = $faker->company;
            $phone = $faker->phoneNumber;
            $template = "
  attendee{$i}:
    Title: Mr
    FirstName: {$firstName}
    Surname: {$lastName}
    Company: {$company}
    Phone: {$phone}
    Email: {$email}";

            $attendees .= $template;
            $attendees .= "\n";
        }

        $registrations = '';
        for ($i=0; $i<20; $i++) {
            $firstName = $faker->firstNameMale;
            $lastName = $faker->lastName;
            $email = $faker->email;
            $parts = \explode('@', $email);
            $email = \strtolower($firstName . '.' . $lastName . '@' . $parts[1]);
            $company = $faker->company;

            $template = "  registration{$i}:
    Event: =>TitleDK\Calendar\Events\Event.conference
    Name: {$firstName} {$lastName}
    PayersName: {$firstName} {$lastName}
    Email: {$email}
    Status: Booked
    NumberOfTickets: 5
    AmountPaid: 20
    Notes: {$faker->bs}";

            $template .= "\n    Attendees:";
            for ($j=0; $j<5; $j++) {
                $ctr = 1+5*$i + $j;
                $template .= "\n      - =>TitleDK\Calendar\Registrations\Attendee.attendee{$ctr}";
            }

            $registrations .= $template;
            $registrations .= "\n\n";
        }
    }
}
