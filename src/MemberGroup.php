<?php

namespace Zirak\MemberUserManagement\Extension;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

class MemberGroup extends DataExtension
{

    /**
     * Remove the administrators group from the possible parent group
     *
     * @todo this check should be done in core code, since the dropdown can be simply
     *                crafted for injecting administrators group ID
     *
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        /* @var $parentID DropdownField */
        $parentID = $fields->fieldByName('Root.Members.ParentID');
        $parentID->setDisabledItems(array(DataObject::get_one('Group', "Code='administrators'")->ID));
    }

    /**
     * Check if the current user can modify the group
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

        if ($member->inGroup('users-manager') && $this->owner->Code !== 'administrators') {
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
        return $this->isAdmin($member);
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canView($member)
    {
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

    /**
     * Add a specific group in order to enable users/groups management
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $group = Group::get()->filter('Code', 'users-manager')->first();
        if (!$group) {
            $usersManagerGroup = Group::create();
            $usersManagerGroup->Code = 'users-manager';
            $usersManagerGroup->Title = _t('Group.DefaultGroupTitleUsersManager', 'Users Manager');
            $usersManagerGroup->Sort = 0;
            $usersManagerGroup->write();
            Permission::grant($usersManagerGroup->ID, 'CMS_ACCESS_SecurityAdmin');
        }
    }
}
