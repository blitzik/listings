<?php

namespace App\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Users\Fixtures\UsersFixture;

class BaseFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //$manager->flush();
    }


    function getDependencies()
    {
        return [
            UsersFixture::class
        ];
    }


}