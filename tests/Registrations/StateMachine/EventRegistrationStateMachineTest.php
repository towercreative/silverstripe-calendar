<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Registrations\StateMachine;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\EventRegistration;
use TitleDK\Calendar\Registrations\StateMachine\EventRegistrationStateMachine;

class EventRegistrationStateMachineTest extends SapphireTest
{
    /** @var \TitleDK\Calendar\Registrations\StateMachine\EventRegistrationStateMachine */
    private $stateMachine;

    /** @throws \SilverStripe\ORM\ValidationException */
    public function setUp(): void
    {
        parent::setUp();

        $registration = EventRegistration::create();
        $registration->write();
        $this->stateMachine = new EventRegistrationStateMachine($registration);
    }


    public function testConstruct(): void
    {
        $this->assertEquals('Available', $this->stateMachine->getStatus());
    }


    public function testAwaitingPayment(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->assertEquals('AwaitingPayment', $this->stateMachine->getStatus());
    }


    public function testPaymentExpired(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentExpired();
        $this->assertEquals('PaymentExpired', $this->stateMachine->getStatus());
    }


    public function testPaymentFailed(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentFailed();
        $this->assertEquals('Unpaid', $this->stateMachine->getStatus());
    }


    public function testTryAgainAfterPaymentFailed(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentFailed();
        $this->stateMachine->tryAgainAfterPaymentFailed();
        $this->assertEquals('AwaitingPayment', $this->stateMachine->getStatus());
    }


    public function testMakeTicketAvailableAfterPaymentTimedOut(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->makeTicketAvailableAfterPaymentTimedOut();
        $this->assertEquals('Available', $this->stateMachine->getStatus());
    }


    public function testPaymentSucceeded(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentSucceeded();
        $this->assertEquals('Paid', $this->stateMachine->getStatus());
    }


    public function testBooked(): void
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentSucceeded();
        $this->stateMachine->booked();
        $this->assertEquals('Booked', $this->stateMachine->getStatus());
    }
}
