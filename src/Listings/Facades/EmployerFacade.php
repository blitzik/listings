<?php declare(strict_types=1);

namespace Listings\Facades;

use Listings\Services\Persisters\EmployerPersister;
use Kdyby\Doctrine\EntityManager;
use Listings\Employer;
use Nette\SmartObject;

class EmployerFacade
{
    use SmartObject;


    /** @var EmployerPersister */
    private $employerPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        EmployerPersister $employerPersister
    ) {
        $this->em = $entityManager;
        $this->employerPersister = $employerPersister;
    }


    /**
     * @param array $values
     * @param Employer|null $employer
     * @return Employer
     */
    public function save(array $values, Employer $employer = null): Employer
    {
        return $this->employerPersister->save($values, $employer);
    }


    public function remove(int $employerId): void
    {
        $this->em->createQuery(
            'DELETE FROM ' . Employer::class . ' e
             WHERE e.id = :id'
        )->execute(['id' => $employerId]);
    }


    public function getEmployer(int $employerId): ?Employer
    {
        return $this->em->find(Employer::class, $employerId);
    }


    /**
     * @param int $userId
     * @return Employer[]
     */
    public function findEmployers(int $userId): array
    {
        return $this->em->createQuery(
            'SELECT e FROM ' . Employer::class . ' e INDEX BY e.id
             WHERE e.user = :userId
             ORDER BY e.createdAt DESC'
        )->setParameter('userId', $userId)
         ->getResult();
    }


    public function findEmployersForSelect(int $userId): array
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