<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Categories;

use SilverStripe\Security\Permission;

/**
 * Public Event Category
 *
 * @package calendar
 * @subpackage categories
 */
class PublicEventCategory extends EventCategory
{

    public function ComingEvents($from = false)
    {
        return $this->Events()
            ->filter(
                array(
                'StartDateTime:GreaterThan' => \date('Y-m-d', $from ? \strtotime($from) : \time())
                ),
            );
    }


    /**
     * Anyone can view public event categories
     *
     * @param \SilverStripe\Security\Member $member
     */
    public function canView(?Member $member = null): bool
    {
        return true;
    }


    public function canCreate(?Member $member = null, $context = []): bool
    {
        return $this->canManage($member);
    }


    public function canEdit(?Member $member = null): bool
    {
        return $this->canManage($member);
    }


    public function canDelete(?Member $member = null): bool
    {
        return $this->canManage($member);
    }


    protected function canManage(Member $member): bool
    {
        return Permission::check('ADMIN', 'any', $member) || Permission::check('EVENTCATEGORY_MANAGE', 'any', $member);
    }
}
