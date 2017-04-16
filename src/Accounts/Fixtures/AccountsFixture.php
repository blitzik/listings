<?php declare(strict_types=1);

namespace Accounts\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use Users\Fixtures\UsersFixture;
use blitzik\Authorization\Role;
use Users\User;

final class AccountsFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultUserRoles($manager);
        $this->loadDefaultAuthorizatorRules($manager);
        $this->loadTestingData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('Accounts:Public:Auth', $manager);
        $ug->addUrl('prihlaseni', 'logIn')
           ->addUrl('odhlaseni', 'logOut');

        $ug->addPresenter('Accounts:Public:Registration')
           ->addUrl('registrace', 'default');

        $ug->addPresenter('Accounts:Public:ForgottenPassword')
           ->addUrl('zapomenute-heslo', 'request')
           ->addUrl('obnova-hesla', 'change');
    }


    private function loadDefaultUserRoles(ObjectManager $objManager)
    {
        $member = new Role(Role::MEMBER);
        $objManager->persist($member);

        $admin = new Role(Role::ADMIN, $member);
        $objManager->persist($admin);

        $this->addReference('role_member', $member);
        $this->addReference('role_admin', $admin);
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        //$arg = new AuthorizationRulesGenerator($manager);
    }


    private function loadTestingData(ObjectManager $manager)
    {
        $member = new User('Lorem', 'ipsum', 'member@project.cz', 'member', $this->getReference('role_member'));
        $manager->persist($member);
        $this->addReference('user_member', $member);

        $member2 = new User('Consecteteur', 'Eligendi', 'member2@project.cz', 'member2', $this->getReference('role_member'));
        $manager->persist($member2);
        $this->addReference('user_member2', $member2);

        $admin = new User('Dolor', 'Sit Amet', 'admin@project.cz', 'admin', $this->getReference('role_admin'));
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);
    }


    function getDependencies()
    {
        return [
            UsersFixture::class
        ];
    }

}