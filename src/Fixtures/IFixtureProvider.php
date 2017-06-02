<?php declare(strict_types=1);

namespace Fixtures;

interface IFixtureProvider
{
    public function getDataFixtures(): array;
}