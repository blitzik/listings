<?php

namespace  Users\DI;

use Kdyby\Doctrine\DI\IEntityProvider;
use Users\Authentication\UserStorage;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\IUserStorage;
use Users\Fixtures\UsersFixture;
use Nette\DI\CompilerExtension;
use Fixtures\IFixtureProvider;
use Nette\Http\Session;
use Nette\DI\Compiler;

class UsersExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    /**
     * Processes configuration data. Intended to be overridden by descendant.
     * @return void
     */
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    /**
     * Adjusts DI container before is compiled to PHP class. Intended to be overridden by descendant.
     * @return void
     */
    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $userStorage = $cb->getDefinition($cb->getByType(IUserStorage::class));
        $userStorage->setClass(
            UserStorage::class,
            ['@'.$cb->getByType(Session::class), '@'.$cb->getByType(EntityManager::class)]
        );
    }


    /**
     * Returns associative array of Namespace => mapping definition
     *
     * @return array
     */
    public function getEntityMappings()
    {
        return ['Users' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                UsersFixture::class
            ]
        ];
    }


}