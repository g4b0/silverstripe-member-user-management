<?php

class ZkMemberGroup extends DataExtension
{

    /**
     * Remove the administrators group from the possible parent group
     * 
     * @todo this check should be done in core code, since the dropdown can be simply 
     *				crafted for injecting administrators group ID
     * @param \FieldList $fields
     */
    public function updateCMSFields(\FieldList $fields)
    {
        parent::updateCMSFields($fields);
        
        /* @var $parentID DropdownField */
        $parentID = $fields->fieldByName('Root.Members.ParentID');
        $parentID->setDisabledItems(array( DataObject::get_one('Group', "Code='administrators'")->ID ));
    }
    
    /**
     * Check if the current user can modify the group
     * @param Member $member
     * @return boolean
     */
    private function isAdmin($member = null)
    {
        $retVal = false;

        $current = Member::currentUser();
        $groups = $current->Groups();

        if (Permission::checkMember($current, 'ADMIN')) {
            return true;
        }
        
        foreach ($groups as $g) {
            if ($g->Code == 'users-manager') {
                if ($this->owner->Code !== 'administrators') {
                    $retVal = true;
                    break;
                }
            }
        }
        return $retVal;
    }

    public function canCreate($member)
    {
        return $this->isAdmin($member);
    }

    public function canEdit($member)
    {
        return $this->isAdmin($member);
    }

    public function canView($member)
    {
        return $this->isAdmin($member);
    }

    public function canDelete($member)
    {
        return $this->isAdmin($member);
    }
    
    /**
     * Add a specific group in order to enable users/groups managemet
     */
    public function requireDefaultRecords()
    {
        $group = DataObject::get('Group', "Code = 'users-manager'");
        if (!$group->count()) {
            $usersManagerGroup = new Group();
            $usersManagerGroup->Code = 'users-manager';
            $usersManagerGroup->Title = _t('Group.DefaultGroupTitleUsersManager', 'Users Manager');
            $usersManagerGroup->Sort = 0;
            $usersManagerGroup->write();
            Permission::grant($usersManagerGroup->ID, 'CMS_ACCESS_SecurityAdmin');
        }
    }
}
