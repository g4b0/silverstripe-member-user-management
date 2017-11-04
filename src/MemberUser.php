<?php

namespace Zirak\MemberUserManagement\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Class MemberUser
 * @package Zirak\MemberUserManagement\Extension
 */
class MemberUser extends DataExtension
{

    /**
     * Check if the current user can modify the user
     *
     * @return boolean
     */
    private function isAdmin()
    {
        $member = Security::getCurrentUser();

        if ($member == null) {
            return false;
        }

        if (Permission::checkMember($member, 'ADMIN')) {
            return true;
        }

        if ($member->inGroup('users-manager') && !Permission::checkMember($this->owner, 'ADMIN')) {
            return true;
        }

        return false;
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canCreate($member)
    {
        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canEdit($member)
    {
        if ($this->owner->ID == Security::getCurrentUser()->ID) {
            return true;
        }

        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canView($member)
    {
        if ($this->owner->ID == Security::getCurrentUser()->ID) {
            return true;
        }

        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canDelete($member)
    {
        return $this->isAdmin($member);
    }
}