<?php

namespace App\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Users\Authorization\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Url\Generators\UrlGenerator;
use Users\Fixtures\UsersFixture;
use Users\Authorization\IRole;
use Users\Authorization\Role;
use Users\User;

class BaseFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultUserRoles($manager);
        $this->loadDefaultAuthorizatorRules($manager);

        $this->loadDefaultData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        // todo
        /*$ug = new UrlGenerator('', $manager);
        $ug->addUrl('', '');*/
    }


    private function loadDefaultUserRoles(ObjectManager $objManager)
    {
        $member = new Role(IRole::MEMBER, 'member');
        $objManager->persist($member);

        $admin = new Role(IRole::ADMIN, 'admin', $member);
        $objManager->persist($admin);

        $this->addReference('role_member', $member);
        $this->addReference('role_admin', $admin);
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager);
    }


    private function loadDefaultData(ObjectManager $manager)
    {
        $member = new User('Lorem', 'ipsum', 'member@project.cz', 'member', $this->getReference('role_member'));
        $manager->persist($member);
        $this->addReference('user_member', $member);
    }


    function getDependencies()
    {
        return [
            UsersFixture::class
        ];
    }


}