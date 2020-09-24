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

    public const AWAITING_PAYMENT = 'AwaitingPayment';

    public const AVAILABLE = 'Available';

    public const PAYMENT_EXPIRED = 'PaymentExpired';

    public const UNPAID = 'Unpaid';

    public const PAID = 'Paid';

    public const BOOKED = 'Booked';

    /** @var \TitleDK\Calendar\Registrations\EventRegistration */
    protected $registration;

    /**
     * EventRegistrationStateMachine constructor.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }


    public function getStatus(): string
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


    private function transitionState(string $from, string $to): void
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
