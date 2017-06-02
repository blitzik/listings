<?php

namespace Accounts\Services\Persisters;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Kdyby\Doctrine\EntityManager;
use blitzik\Authorization\Role;
use Kdyby\Monolog\Logger;
use Nette\SmartObject;
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
     * @param User|null $user
     * @return User
     * @throws \Exception
     * @throws EmailIsInUseException
     */
    public function save(array $values, User $user = null): User
    {
        try {
            $this->em->beginTransaction();

            if (isset($user) and $user->getId() !== null) {
                $newUser = $this->update($values, $user);
            } else {
                $newUser = $this->create($values);
            }

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
        $role = $this->em->createQuery(
            'SELECT r FROM ' . Role::class . ' r
             WHERE r.name = :name'
        )->setParameter('name', Role::MEMBER)
         ->getOneOrNullResult();

        $user = new User(
            $values['firstName'],
            $values['lastName'],
            $values['email'],
            $values['pass'],
            $role
        );

        $this->em->persist($user);
        try {
            $this->em->flush();

        } catch (UniqueConstraintViolationException $e) {
            throw new EmailIsInUseException;
        }

        return $user;
    }


    /**
     * @param array $values
     * @param User $user
     * @return User
     */
    private function update(array $values, User $user): User
    {
        $user->setFirstName($values['firstName']);
        $user->setLastName($values['lastName']);

        $this->em->flush();

        return $user;
    }
}