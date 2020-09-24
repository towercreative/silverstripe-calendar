<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Helpers\ICSExportHelper;

//by Anselm
/**
 * ICS Export controller
 *
 * Usage:
 * * "all" link is "/ics/all", like this: "/ics/all?dump=1" for debugging.
 * * Example links (will only work once you've called the get methods at least once):
 *   * Excursion calendar: /ics/cal/excursions
 *
 * More explaination:
 * https://github.com/colinburns/daypack.com.au/issues/48
 */
class ICSExport_Controller extends Controller
{

    private static $allowed_actions = [
        'cal';
    private 'all';
    private 'my';
    private ];

    public function init(): void
    {
        parent::init();
    }


    public function index()
    {
        return false;
    }


    /**
     * Single calendar
     * Can be public or private
     * For public calendars either the calendar id or the calendar url can be supplied
     * For private calendars user email and hash need to be supplied - like this.
     * The private calendar hash is created in {@see PrivateCalendarMemberExtension}
     */
    public function cal()
    {
        $cal = null;
        $request = $this->getRequest();

        $ics = null;

        $idOrURL = $request->param('ID');

        //echo $idOrURL;

        //Public calendar via id
        if (\is_numeric($idOrURL)) {
            //calendar id is requested
            //echo 'request is numeric';
            $cal = Calendar::get()->ByID((int) $request->param('ID'));

            //echo $cal->getLink();

            //Public calendar via url
        } else {
            //calendar url is requested
            //echo 'request is a string';
            $url = Convert::raw2url($idOrURL);

            // @todo This needs changed to slug
            $cal = Calendar::get()
                ->filter('Slug', $url)
                ->First();
        }

        //If not public calendar is found we check for a private calendar
        if (!$cal) {
            echo $idOrURL;
            $member = Member::get()
                ->filter(Email::class, $idOrURL)
                ->filter('PrivateCalendarKey', $request->param('OtherID'))
                ->First();
            if ($member && $member->exists()) {
                return $this->memberCalendar($member);
            }
        }



        if ($cal && $cal->exists()) {
            //everybody can access public calendars
            if ($cal->ClassName === 'Calendar') {
                $helper = new ICSExportHelper();
                $ics = $helper->processCalendar($cal);
                $calName = $cal->Title;
            }

            return $this->output($ics, $calName);
        } else {
            echo "calendar can't be found";
        }
    }


    /**
     * All public calendars
     */
    public function all()
    {
        $calendars = Calendar::get();
        $events = new ArrayList();
        foreach ($calendars as $cal) {
            $events->merge($cal->Events());
        }

        $eventsArr = $events->toNestedArray();

        $ics = new ICSExport($eventsArr);

        return $this->output($ics, 'all');
    }


    /**
     * The currently logged in user's calendar
     */
    public function my()
    {
        $member = Member::currentUser();
        if (!$member) {
            return 'please log in';
        }

        return $this->memberCalendar($member);
    }


    protected function memberCalendar($member)
    {
        $events = PrivateEvent::get()
            ->filter(
                [
                'OwnerID' => $member->ID,
                ],
            )
            ->filter(
                [
                'StartDateTime:GreaterThan' => PrivateCalendarController::offset_date('start', null, 300),
                'EndDateTime:LessThan' => PrivateCalendarController::offset_date('end', null, 300),
                ],
            );

        $eventsArr = $events->toNestedArray();

        $ics = new ICSExport($eventsArr);

        return $this->output($ics, \strtolower($member->FirstName));
    }


    protected function output(?ICSExport $ics, $name): void
    {
        if (!$ics) {
            return;
        }

        if (!isset($_GET['dump'])) {
            //normal mode
            return $ics->getFile("$name.ics");
        }

        //dump/debug mode
        echo "<pre>";
        echo $ics->getString();
        echo "</pre>";
    }
}
