<?php declare(strict_types=1);

namespace Users\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Authorization\Privilege;

class UsersFixture extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadDefaultPrivileges($manager);

        $manager->flush();
    }


    private function loadDefaultPrivileges(ObjectManager $manager): void
    {
        $create = new Privilege(Privilege::CREATE);
        $manager->persist($create);
        $this->setReference('privilege_create', $create);

        $edit = new Privilege(Privilege::EDIT);
        $manager->persist($edit);
        $this->setReference('privilege_edit', $edit);

        $remove = new Privilege(Privilege::REMOVE);
        $manager->persist($remove);
        $this->setReference('privilege_remove', $remove);

        $view = new Privilege(Privilege::VIEW);
        $manager->persist($view);
        $this->setReference('privilege_view', $view);
    }

}