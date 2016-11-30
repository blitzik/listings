<?php

namespace  Users\DI;

use Users\Authorization\AuthorizationAssertionsCollection;
use Users\Authorization\IAuthorizationAssertion;
use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;
use Users\Authorization\Authorizator;
use Users\Authentication\UserStorage;
use App\Fixtures\IFixtureProvider;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\IUserStorage;
use Users\Fixtures\UsersFixture;
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

        $authorizator = $cb->addDefinition($this->prefix('authorizator'));
        $authorizator->setClass(Authorizator::class);
        
        $assertionCollection = $cb->addDefinition($this->prefix('AuthorizationAssertionsCollection'));
        $assertionCollection->setClass(AuthorizationAssertionsCollection::class);
        foreach ($cb->findByType(IAuthorizationAssertion::class) as $assertion) {
            $assertionCollection->addSetup('addAssertion', ['assertion' => $assertion]);
        }
        $authorizator->setArguments(['assertionsCollection' => $assertionCollection]);
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