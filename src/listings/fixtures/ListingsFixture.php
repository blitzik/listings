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

final class ListingsFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);
        $this->loadTestingData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('Listings:Dashboard', $manager);
        $ug->addUrl('', 'default');

        $ug->addPresenter('Listings:Listing')
           ->addUrl('nova-vycetka', 'new')
           ->addUrl('uprava-vycetky', 'edit');

        $ug->addPresenter('Listings:ListingDetail')
            ->addUrl('detail-vycetky', 'default');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager);
        $arg->addResource(new Resource(Listing::class))
            ->addDefinition($this->getReference('privilege_view'), $this->getReference('role_member'));
    }


    private function loadTestingData(ObjectManager $manager)
    {
        $users = [$this->getReference('user_member'), $this->getReference('user_member2'), $this->getReference('user_admin')];
        foreach ([2014, 2015, 2016] as $year) {
            for ($month = 0; $month < 12; $month++) {
                for ($j = 0; $j < 2; $j++) {
                    $l = new Listing($users[rand(0, 2)], $year, $month + 1);
                    $manager->persist($l);
                    for ($day = 1; $day <= 10; $day++) {
                        $item = new ListingItem($l, $day, 'Locality name', '06:00', '16:00', '01:00');
                        $manager->persist($item);
                    }
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