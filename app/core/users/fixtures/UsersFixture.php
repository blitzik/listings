<?php

namespace Users\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Users\Authorization\Privilege;

class UsersFixture extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultPrivileges($manager);

        $manager->flush();
    }


    private function loadDefaultPrivileges(ObjectManager $manager)
    {
        $create = new Privilege('create');
        $manager->persist($create);
        $this->setReference('privilege_create', $create);

        $edit = new Privilege('edit');
        $manager->persist($edit);
        $this->setReference('privilege_edit', $edit);

        $remove = new Privilege('remove');
        $manager->persist($remove);
        $this->setReference('privilege_remove', $remove);

        $view = new Privilege('view');
        $manager->persist($view);
        $this->setReference('privilege_view', $view);

        $upload = new Privilege('upload');
        $manager->persist($upload);
        $this->setReference('privilege_upload', $upload);
    }

}