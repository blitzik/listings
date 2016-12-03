<?php

namespace Listings\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Users\Authorization\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Accounts\Fixtures\AccountsFixture;
use Users\Authorization\Resource;
use Url\Generators\UrlGenerator;
use Listings\ListingItem;
use Listings\Listing;

class ListingsFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);
        $this->loadTestingData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('Listings:Dashboard', $manager);
        $ug->addUrl('', 'default');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        //$arg = new AuthorizationRulesGenerator(new Resource('TODO'), $manager); // todo
    }


    private function loadTestingData(ObjectManager $manager)
    {
        for ($month = 0; $month < 12; $month++) {
            for ($j = 0; $j < 2; $j++) {
                $l = new Listing($this->getReference('user_member'), 2016, $month + 1);
                $manager->persist($l);
                for ($day = 1; $day <= 10; $day++) {
                    $item = new ListingItem($l, $day, 'Borovnice', '06:00', '16:00', '01:00');
                    $manager->persist($item);
                }
            }
        }
    }


    function getDependencies()
    {
        return [
            AccountsFixture::class
        ];
    }

}