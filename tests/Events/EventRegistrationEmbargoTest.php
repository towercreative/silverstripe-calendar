<?php
namespace TitleDK\Calendar\Tests\Events;

use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventRegistrationEmbargoTest extends SapphireTest
{

    /** @var Carbon */
    protected $now;

    /** @var Event */
    protected $event;



    public function setUp()
    {
        parent::setUp();
    }

    public function test_default_embargo_date()
    {
    }
}
