<?php

declare(strict_types = 1);

namespace Listings\Facades;

use Kdyby\Doctrine\EntityManager;
use Listings\Employer;
use Nette\SmartObject;

class EmployerFacade
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @param string $employerId
     * @return null|Employer
     */
    public function getEmployer($employerId)
    {
        return $this->em->find(Employer::class, $employerId);
    }


    /**
     * @param string $userId
     * @return array
     */
    public function findEmployersForSelect($userId): array
    {
        $result = $this->em->createQuery(
            'SELECT e.id, e.name FROM ' . Employer::class . ' e
             WHERE e.user = :user'
        )->setParameter('user', $userId)
         ->getArrayResult();

        if (empty($result)) {
            return [];
        }

        return array_column($result, 'name', 'id');
    }
    
    
}