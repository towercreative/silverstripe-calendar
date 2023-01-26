<?php declare(strict_types=1);

namespace TitleDK\Calendar\Calendars;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;
use SilverStripe\Security\Member;

// @phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Calendar Model
 * The calendar serves as a holder for events, but events can exist as instances on their own.
 *
 * @package calendar
 * @subpackage calendars
 * @property string $Slug
 * @property string $Color
 * @property string $Title
 * @method \SilverStripe\ORM\DataList|array<\TitleDK\Calendar\Events\Event> Events()
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\PageTypes\CalendarPage> CalendarPages()
 * @method \SilverStripe\ORM\ManyManyList|array<\SilverStripe\Security\Group> Groups()
 * @mixin \TitleDK\Calendar\Colors\CalendarColorExtension
 */
class Calendar extends DataObject
{
    private static $table_name = 'Calendar';

    private static $db = [
        'Title' => 'Varchar',
    ];

    private static $has_many = [
        'Events' => Event::class,
    ];

    private static $default_sort = 'Title';

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    //Public calendars are simply called 'Calendar'
    private static $singular_name = 'Calendar';
    private static $plural_name = 'Calendars';

    // for applying group restrictions
    private static $belongs_many_many = [
        'Groups' => Group::class,
    ];

    private static $many_many = [
        'CalendarPages' => CalendarPage::class,
    ];


    public function getCMSFields(): \SilverStripe\Forms\FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $groupsMap = [];
            foreach (Group::get() as $group) {
                // Listboxfield values are escaped, use ASCII char instead of &raquo;
                $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
            }
            \asort($groupsMap);

            $fields->addFieldToTab(
                'Root.Main',
                ListboxField::create('Groups', Group::singleton()->i18n_plural_name())
                    ->setSource($groupsMap)
                    ->setAttribute(
                        'data-placeholder',
                        \_t(self::class . '.ADDGROUP', 'Add group restriction', 'Placeholder text for a dropdown'),
                    )->setRightTitle(
                        'Only these groups will be able to see this calendar and events, leave empty for public',
                    ),
            );
        });

        $fields = parent::getCMSFields();

        //Events shouldn't be editable from here by default
        $fields->removeByName('Events');

        /** @var GridField $calendarPages */
        if ($calendarPages = $fields->dataFieldByName('CalendarPages')) {
            $calendarPages->getConfig()
                ->removeComponentsByType([
                    GridFieldAddExistingAutocompleter::class,
                ])->addComponents([
                    new GridFieldAddExistingSearchButton()
                ]);
        }

        return $fields;
    }


    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return true;
    }


    // @todo Check context iterative type

    /**
     * @param Member $member
     * @param array $context Additional context-specific data which might
     * affect whether (or where) this object could be created.
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return $this->canManage($member);
    }


    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return $this->canManage($member);
    }


    /**
     * @param Member $member
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return $this->canManage($member);
    }


    protected function canManage(Member $member = null): bool
    {
        return Permission::check('ADMIN', 'any', $member) || Permission::check('CALENDAR_MANAGE', 'any', $member);
    }
}
