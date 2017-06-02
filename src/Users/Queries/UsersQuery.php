<?php declare(strict_types=1);

namespace Users\Queries;

use Users\Exceptions\Logic\InvalidArgumentException;
use blitzik\Authorization\Role;
use Kdyby\Doctrine\QueryObject;
use Nette\Utils\Validators;
use Users\User;
use Kdyby;

class UsersQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function onlyWithFields(array $fields): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($fields) {
            $qb->select(sprintf('PARTIAL u.{%s}', implode(',', $fields)));
        };

        return $this;
    }
    
    
    public function notClosed(): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->andWhere('u.isClosed = false');
        };
    
        return $this;
    }


    public function onlyIds(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->select('PARTIAL u.{id}');
        };

        return $this;
    }


    public function byLastName(string $lastName): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($lastName) {
            $qb->andWhere('u.lastName LIKE :lastName')->setParameter('lastName', $lastName . '%');
        };

        return $this;
    }


    public function byEmail(string $email): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($email) {
            $qb->andWhere('u.email LIKE :email')->setParameter('email', $email . '%');
        };

        return $this;
    }


    public function byRole($role): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($role) {
            $roleId = null;
            if ($role instanceof Role) {
                $roleId = $role->getId();
            } elseif (Validators::is($role, 'numericint')) {
                $roleId = $role;
            } else {
                throw new InvalidArgumentException(sprintf('Only instances of %s and integer numbers can pass. "%s" given', Role::class, gettype($role)));
            }

            $qb->andWhere('u.role = :roleId')->setParameter('roleId', $roleId);
        };

        return $this;
    }


    public function byRoles(array $roles): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($roles) {
            $roleIds = null;
            foreach ($roles as $role) {
                if ($role instanceof Role) {
                    $roleIds[] = $role->getId();
                } elseif (Validators::is($role, 'numericint')) {
                    $roleIds[] = $role;
                } else {
                    throw new InvalidArgumentException(sprintf('Only instances of %s and integer numbers can pass. "%s" given', Role::class, gettype($role)));
                }
            }

            $qb->andWhere('u.role IN (:roleIds)')->setParameter('roleIds', $roleIds);
        };

        return $this;
    }
    
    
    public function byId($id): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('u.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    public function byIds($ids): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($ids) {
            $qb->andWhere('u.id IN (:ids)')->setParameter('ids', $ids);
        };

        return $this;
    }


    public function indexedById(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->indexBy('u', 'u.id');
        };

        return $this;
    }

    
    public function orderByLastName(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->orderBy('COLLATE(u.lastName, utf8_czech_ci)', $order);
        };
    
        return $this;
    }
    

    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('u, r')
           ->join('u.role', 'r');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(User::class, 'u');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}