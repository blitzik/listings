<?php declare(strict_types=1);

namespace Fixtures;

interface IFixtureProvider
{
    /**
     * @return array
     */
    public function getDataFixtures();
}