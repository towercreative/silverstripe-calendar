<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations\StateMachine;

/**
 * Event Model
 *
 * Events must be associated with a calendar
 *
 * @package calendar
 */
class EventRegistrationStateMachine
{

    const AWAITING_PAYMENT = 'AwaitingPayment';

    const AVAILABLE = 'Available';

    const PAYMENT_EXPIRED = 'PaymentExpired';

    const UNPAID = 'Unpaid';

    const PAID = 'Paid';

    const BOOKED = 'Booked';

    /** @var \TitleDK\Calendar\Registrations\StateMachine\EventRegistration */
    protected $registration;

    public function __construct($registration)
    {
        $this->registration = $registration;
    }


    public function getStatus()
    {
        return $this->registration->Status;
    }


    /**
     * Registration has moved from available to awaiting payment
     */
    public function awaitingPayment(): void
    {
        $this->transitionState(self::AVAILABLE, self::AWAITING_PAYMENT);
    }


    /**
     * Mark a payment as expired. This should happen from cron normally, after say 20 mins
     */
    public function paymentExpired(): void
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::PAYMENT_EXPIRED);
    }


    /**
     * Mark payment failed
     */
    public function paymentFailed(): void
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::UNPAID);
    }


    /**
     * Mark payment failed
     */
    public function tryAgainAfterPaymentFailed(): void
    {
        $this->transitionState(self::UNPAID, self::AWAITING_PAYMENT);
    }


    /**
     * Mark payment failed
     */
    public function makeTicketAvailableAfterPaymentTimedOut(): void
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::AVAILABLE);
    }


    /**
     * Mark a payment as succeeded
     */
    public function paymentSucceeded(): void
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::PAID);
    }


    /**
     * Tickets are booked
     */
    public function booked(): void
    {
        $this->transitionState(self::PAID, self::BOOKED);
    }

    // @todo Cancel
    private function transitionState($from, $to): void
    {
        // @todo Check validity of from and to
        // @todo Can events be thrown?
        $currentState = $this->registration->Status;
        if ($currentState !== $from) {
            throw new \InvalidArgumentException(
                "Registration {$this->registration->ID} was in state "
                . "{$currentState}, was expecting {$from}",
            );
        }

        $this->registration->Status = $to;
        $this->registration->write();
    }
}
