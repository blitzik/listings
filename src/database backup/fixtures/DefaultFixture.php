<?php

namespace DatabaseBackup\Fixtures;

use Users\Authorization\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Users\Authorization\Resource;
use Url\Generators\UrlGenerator;

class DefaultFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('DatabaseBackup:CronBackup', $manager); // todo
        $ug->addUrl('auto-backup', 'backup');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        //$arg = new AuthorizationRulesGenerator(new Resource('TODO'), $manager); // todo
    }

}