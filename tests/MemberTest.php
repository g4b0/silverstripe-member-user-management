<?php

namespace Zirak\MemberUserManagement\Test;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;

/**
 * Class MemberTest
 * @package Zirak\MemberUserManagement\Test
 */
class MemberTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testCanCreate()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $permission = $this->objFromFixture(Member::class, 'permission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');

        $this->assertFalse(Injector::inst()->get(Member::class)->canCreate($noPermission));
        $this->assertTrue(Injector::inst()->get(Member::class)->canCreate($permission));
        $this->assertFalse(Injector::inst()->get(Member::class)->canCreate($noPermission2));
        $this->assertFalse(Injector::inst()->get(Member::class)->canCreate());
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testCanEdit()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $this->assertTrue($noPermission->canEdit($noPermission));
        $this->assertTrue($noPermission->canEdit($permission));
        $this->assertFalse($noPermission->canEdit($noPermission2));
        $this->assertFalse($noPermission->canEdit());
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $this->assertFalse($noPermission->canDelete($noPermission));
        $this->assertTrue($noPermission->canDelete($permission));
        $this->assertFalse($noPermission->canDelete($noPermission2));
        $this->assertFalse($noPermission->canDelete());
    }

    /**
     *
     */
    public function testCanView()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $toTest = Member::get()->byID($noPermission->ID);

        $this->assertTrue($toTest->canView($noPermission));
        $this->assertTrue($toTest->canView($permission));
        $this->assertFalse($toTest->canView($noPermission2));
        $this->assertFalse($noPermission->canView());
    }
}