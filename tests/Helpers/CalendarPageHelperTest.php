<?php

namespace TitleDK\Calendar\Tests\Registrations\Helpers;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Helpers\CalendarPageHelper;

class CalendarPageHelperTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml'];

    /** @var CalendarPageHelper */
   private $calendarPageHelper;

   public function setUp()
   {
        parent::setUp();
        $this->calendarPageHelper = new CalendarPageHelper();
   }

    public function test_perform_search_start_of_title_camel_case()
    {
        $titles = $this->getEventTitlesForSearch('SilverSt');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }

    public function test_perform_search_start_of_title_lower_case()
    {
        $titles = $this->getEventTitlesForSearch('silverstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }

    public function test_perform_search_middle_of_title_camel_case()
    {
        $titles = $this->getEventTitlesForSearch('verStr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('Booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }

    public function test_perform_search_middle_of_title_lower_case()
    {
        $titles = $this->getEventTitlesForSearch('verstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }

    /**
     * @param $q
     * @return array
     */
    private function getEventTitlesForSearch($q)
    {
        $searchResults = $this->calendarPageHelper->performSearch($q)->toArray();
        $titles = array_map(function ($event) {
            return $event->Title;
        }, $searchResults);
        return $titles;
    }
}
