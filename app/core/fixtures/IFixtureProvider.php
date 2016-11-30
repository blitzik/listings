<?php

namespace App\Fixtures;

interface IFixtureProvider
{
    /**
     * @return array
     */
    public function getDataFixtures();
}