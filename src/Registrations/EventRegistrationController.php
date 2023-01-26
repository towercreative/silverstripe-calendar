<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Registrations;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;

/**
 * Event Registration Controller
 *
 * @package calendar
 * @subpackage registrations
 * @mixin \TitleDK\Calendar\Registrations\Controller\AttendeesControllerExtension
 */
class EventRegistrationController extends Controller
{

    private static $allowed_actions = [
        'registerform',
        'paymentregisterform',
    ];

    /* This is in the routes file */
    private static $url_segment = 'calregistrations';


    public function init(): void
    {
        parent::init();
    }


    public function registerform(): EventRegistrationForm
    {
        $form = EventRegistrationForm::create(
            $this,
            'registerform'
        );

        $completeGetVar = $this->request->getVar('complete');
        if (isset($completeGetVar)) {
            $form->setDone();
        }


        if ($form->hasExtension(FormSpamProtectionExtension::class)) {
            $form->enableSpamProtection();
        }

        return $form;
    }


    /**
     * This method is called both during GET viewing the form and POST submitting the form
     */
    public function paymentregisterform(): PaymentRegistrationForm
    {
        $form = PaymentRegistrationForm::create(
            $this,
            'paymentregisterform'
        );

        if ($form->hasExtension(FormSpamProtectionExtension::class)) {
            $form->enableSpamProtection();
        }

        return $form;
    }


    /** @param array<string, string|int|float|bool>|null $retVars */
    public function handleJsonResponse(bool $success = false, ?array $retVars = null): \SS_HTTPResponse
    {
        $result = [];
        if ($success) {
            $result = [
                'success' => $success,
            ];
        }
        if (!\is_null($retVars)) {
            $result = \array_merge($retVars, $result);
        }

        $response = new HTTPResponse(\json_encode($result));
        $response->addHeader('Content-Type', 'application/json');

        return $response;
    }
}
