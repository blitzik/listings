<?php declare(strict_types= 1);

namespace Accounts\Facades;

use Accounts\Exceptions\Runtime\EmailSendingFailedException;
use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Accounts\Exceptions\Runtime\UserNotFoundException;
use Accounts\Services\ForgottenPasswordEmailSender;
use Accounts\Services\Persisters\UserPersister;
use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;
use Users\User;

class AccountFacade
{
    use SmartObject;


    /** @var ForgottenPasswordEmailSender */
    private $forgottenPasswordEmailSender;

    /** @var UserPersister */
    private $userPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        UserPersister $userPersister,
        ForgottenPasswordEmailSender $forgottenPasswordEmailSender
    ) {
        $this->em = $entityManager;
        $this->userPersister = $userPersister;
        $this->forgottenPasswordEmailSender = $forgottenPasswordEmailSender;
    }


    /**
     * @param array $values
     * @return \Users\User
     * @throws EmailIsInUseException
     * @throws \Exception
     */
    public function createAccount(array $values): User
    {
        return $this->userPersister->save($values);
    }


    public function updateAccount(array $values, User $user): void
    {
        $user->setFirstName($values['first_name']);
        $user->setLastName($values['last_name']);

        $this->em->flush();
    }


    public function getUserByEmail(string $email): ?User
    {
        $user = $this->em->createQuery(
            'SELECT u FROM ' . User::class . ' u
             WHERE u.email = :email'
        )->setParameter('email', $email)
         ->getOneOrNullResult();

        return $user;
    }


    /**
     * @param string $recipientEmail
     * @param string $senderEmail
     * @param string $applicationUrl
     * @param string $adminFullName
     * @throws UserNotFoundException
     * @throws EmailSendingFailedException
     */
    public function restorePassword(
        string $recipientEmail,
        string $senderEmail,
        string $applicationUrl,
        string $adminFullName
    ): void {
        $user = $this->getUserByEmail($recipientEmail);
        if ($user === null) {
            throw new UserNotFoundException;
        }

        $this->forgottenPasswordEmailSender
             ->send(
                 $user,
                 $senderEmail,
                 $applicationUrl,
                 $adminFullName
             );
    }


    public function changePassword(User $user, string $newPassword): void
    {
        $user->changePassword($newPassword);

        $this->em->flush();
    }

}