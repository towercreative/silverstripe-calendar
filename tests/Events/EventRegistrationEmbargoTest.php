<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Events;

use SilverStripe\Dev\SapphireTest;

class EventRegistrationEmbargoTest extends SapphireTest
{

    /** @var \Carbon\Carbon */
    protected $now;

    /** @var \TitleDK\Calendar\Events\Event */
    protected $event;



    public function setUp(): void
    {
        parent::setUp();
    }


    public function test_default_embargo_date(): void
    {
    }
}
