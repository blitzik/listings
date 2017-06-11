<?php

namespace Listings\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use Accounts\Fixtures\AccountsFixture;
use Listings\Utils\Time\ListingTime;
use blitzik\Authorization\Resource;
use Listings\ListingSettings;
use Listings\ListingItem;
use Listings\Listing;

final class ListingsFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);
        $this->loadDefaultListingSettings($manager);
        //$this->loadTestingData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager): void
    {
        $ug = new UrlGenerator('Listings:Member:Dashboard', $manager);
        $ug->addUrl('', 'default');

        $ug->addPresenter('Listings:Member:Listing')
           ->addUrl('vycetka/zalozeni', 'new')
           ->addUrl('vycetka/uprava', 'edit')
           ->addUrl('vycetka/zruseni', 'remove');

        $ug->addPresenter('Listings:Member:ListingDetail')
           ->addUrl('vycetka/detail', 'default');

        $ug->addPresenter('Listings:Member:ListingItem')
           ->addUrl('vycetka/polozka', 'default');

        $ug->addPresenter('Listings:Member:ListingPdfGeneration')
           ->addUrl('vycetka/generovani-pdf', 'default');

        $ug->addPresenter('Listings:Member:EmployersOverview')
           ->addUrl('zamestnavatele', 'default');

        $ug->addPresenter('Listings:Public:ListingPdf')
           ->addUrl('pdf-sablony', 'default');

        $ug->addPresenter('Listings:Member:Profile')
           ->addUrl('profil', 'default');

        $ug->addPresenter('Listings:Member:Settings')
           ->addUrl('nastaveni', 'default');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager): void
    {
        $arg = new AuthorizationRulesGenerator($manager);
        $arg->addResource(new Resource(Listing::class))
            ->addDefinition($this->getReference('privilege_edit'), $this->getReference('role_member'))
            ->addDefinition($this->getReference('privilege_remove'), $this->getReference('role_member'))
            ->addDefinition($this->getReference('privilege_view'), $this->getReference('role_member'));
    }


    private function loadDefaultListingSettings(ObjectManager $manager): void
    {
        $memberListingSetting = new ListingSettings(
            $this->getReference('user_member'),
            Listing::ITEM_TYPE_LUNCH_SIMPLE,
            new ListingTime('06:00'), new ListingTime('16:00'),
            new ListingTime('11:00'), new ListingTime('12:00' )
        );
        $manager->persist($memberListingSetting);

        $member2ListingSetting = new ListingSettings(
            $this->getReference('user_member2'),
            Listing::ITEM_TYPE_LUNCH_SIMPLE,
            new ListingTime('06:00'), new ListingTime('16:00'),
            new ListingTime('11:00'), new ListingTime('12:00' )
        );
        $manager->persist($member2ListingSetting);

        $adminListingSetting = new ListingSettings(
            $this->getReference('user_admin'),
            Listing::ITEM_TYPE_LUNCH_SIMPLE,
            new ListingTime('06:00'), new ListingTime('16:00'),
            new ListingTime('11:00'), new ListingTime('12:00' )
        );
        $manager->persist($adminListingSetting);
    }


    private function loadTestingData(ObjectManager $manager): void
    {
        $users = [$this->getReference('user_member'), $this->getReference('user_member2'), $this->getReference('user_admin')];
        foreach ([2014, 2015, 2016] as $year) {
            for ($month = 0; $month < 12; $month++) {
                for ($j = 0; $j < 2; $j++) {
                    $l = new Listing($users[rand(0, 2)], $year, $month + 1, Listing::ITEM_TYPE_LUNCH_SIMPLE);
                    $manager->persist($l);
                    for ($day = 1; $day <= 10; $day++) {
                        $item = new ListingItem($l, $day, 'Locality name', '06:00', '16:00', '01:00');
                        $manager->persist($item);
                    }
                }
            }
        }
    }


    function getDependencies(): array
    {
        return [
            AccountsFixture::class
        ];
    }

}