<?php declare(strict_types=1);

namespace DatabaseBackup\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;

class DefaultFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager): void
    {
        $ug = new UrlGenerator('DatabaseBackup:CronBackup', $manager); // todo
        $ug->addUrl('auto-backup', 'backup');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager): void
    {
        //$arg = new AuthorizationRulesGenerator(new Resource('TODO'), $manager); // todo
    }

}