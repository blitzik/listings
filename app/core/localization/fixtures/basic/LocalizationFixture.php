<?php

namespace Localization\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Localization\Locale;

class LocalizationFixture extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $cs_CZ = new Locale('cs_CZ', 'cs', 'Čeština', true);
        $manager->persist($cs_CZ);

        $manager->flush();
    }

}