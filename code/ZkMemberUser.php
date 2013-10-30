<?php

class ZkMemberUser extends DataExtension {

	/**
	 * Check if the current user can modify the user
	 * @param Member $member
	 * @return boolean
	 */
	private function isAdmin($member = null) {
		$retVal = false;

		$current = Member::currentUser();
		$groups = $current->Groups();

		if (Permission::checkMember($current, 'ADMIN')) {
			return true;
		}

		foreach ($groups as $g) {
			if ($g->Code == 'users-manager') {
				if (!Permission::checkMember($this->owner, 'ADMIN')) {
					$retVal = true;
					break;
				}
			}
		}
		
		return $retVal;
	}

	public function canCreate($member) {
		return $this->isAdmin($member);
	}

	public function canEdit($member) {
		if ($this->owner->ID == Member::currentUserID()) {
			return true;
		}
		return $this->isAdmin($member);
	}

	public function canView($member) {
		return $this->isAdmin($member);
	}

	public function canDelete($member) {
		return $this->isAdmin($member);
	}

}

?>
