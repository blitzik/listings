<?php declare(strict_types=1);

namespace Listings\Services\Persisters;

use Kdyby\Doctrine\EntityManager;
use Listings\Employer;
use Nette\SmartObject;
use Users\User;

class EmployerPersister
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @param array $values
     * @param Employer|null $employer
     * @return Employer
     */
    public function save(array $values, Employer $employer = null): Employer
    {
        if (isset($employer) and $employer->getId() !== null) {
            $emp = $this->update($values, $employer);
        } else {
            $emp = $this->create($values);
        }


        return $emp;
    }


    /**
     * @param array $values
     * @return Employer
     */
    private function create(array $values): Employer
    {
        $user = $this->em->find(User::class, $values['user']);
        $employer = new Employer($values['name'], $user);

        $this->em->persist($employer)->flush();

        return $employer;
    }


    /**
     * @param array $values
     * @param Employer $employer
     * @return Employer
     */
    private function update(array $values, Employer $employer): Employer
    {
        $employer->setName($values['name']);

        $this->em->flush();

        return $employer;
    }
}