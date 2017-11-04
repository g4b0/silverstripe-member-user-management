<?php

namespace Zirak\MemberUserManagement\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
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
     * @param Member $member
     *
     * @return boolean
     */
    private function isAdmin(Member $member)
    {
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
     * @param Member $member
     *
     * @return bool
     */
    public function canCreate($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        if ($this->owner->ID == $member->ID) {
            return true;
        }

        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canView($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        if ($member === null) {
            return false;
        }

        if ($this->owner->ID == $member->ID) {
            return true;
        }

        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canDelete($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        return $this->isAdmin($member);
    }
}