<?php

namespace Users\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Users\Authorization\Role;
use Kdyby;

class RoleQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function byId($id)
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('r.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    public function byName($name)
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($name) {
            $qb->andWhere('r.name = :name')->setParameter('name', $name);
        };

        return $this;
    }
    

    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('r');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Role::class, 'r');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}