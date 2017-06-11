<?php

namespace Accounts\Services\Persisters;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Listings\Utils\Time\ListingTime;
use Kdyby\Doctrine\EntityManager;
use blitzik\Authorization\Role;
use Listings\ListingSettings;
use Kdyby\Monolog\Logger;
use Nette\SmartObject;
use Listings\Listing;
use Users\User;

class UserPersister
{
    use SmartObject;


    /** @var \Kdyby\Monolog\CustomChannel */
    private $logger;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        Logger $logger
    ) {
        $this->em = $entityManager;
        $this->logger = $logger->channel('registrations');
    }


    public function closeManager(): void
    {
        $this->em->rollback();
        $this->em->close();
    }


    /**
     * @param array $values
     * @return User
     * @throws \Exception
     * @throws EmailIsInUseException
     */
    public function save(array $values): User
    {
        try {
            $this->em->beginTransaction();

            $newUser = $this->create($values);

            $this->em->commit();

        } catch (EmailIsInUseException $e) {
            $this->closeManager();
            throw $e;

        } catch (\Exception $e) {
            $this->closeManager();

            $this->logger->addCritical(sprintf('User\'s E-mail: [%s]', $values['email']));

            throw $e;
        }


        return $newUser;
    }


    /**
     * @param array $values
     * @return User
     * @throws EmailIsInUseException
     * @throws \Exception
     */
    private function create(array $values): User
    {
        /** @var Role $role */
        $role = $this->em->createQuery(
            'SELECT r FROM ' . Role::class . ' r
             WHERE r.name = :name'
        )->setParameter('name', Role::MEMBER)
         ->getResult();

        $user = new User(
            $values['firstName'],
            $values['lastName'],
            $values['email'],
            $values['pass'],
            $role
        );
        $this->em->persist($user);

        $listingSettings = new ListingSettings(
            $user,
            Listing::ITEM_TYPE_LUNCH_RANGE,
            new ListingTime('06:00'), new ListingTime('14:30'),
            new ListingTime('10:00'), new ListingTime('10:30' )
        );
        $this->em->persist($listingSettings);

        try {
            $this->em->flush();

        } catch (UniqueConstraintViolationException $e) {
            throw new EmailIsInUseException;
        }

        return $user;
    }
}