<?php

declare(strict_types = 1);

namespace Listings\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Listings\Listing;
use Users\User;
use Kdyby;

final class ListingQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    /**
     * @param string $id
     * @return ListingQuery
     */
    public function byId(string $id): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('l.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    /**
     * @param User $owner
     * @return ListingQuery
     */
    public function byOwner(User $owner): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($owner) {
            $qb->andWhere('l.owner = :owner')->setParameter('owner', $owner);
        };

        return $this;
    }


    /**
     * @param string $owner
     * @return ListingQuery
     */
    public function byOwnerId(string $owner): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($owner) {
            $qb->andWhere('l.owner = :owner')->setParameter('owner', $owner);
        };

        return $this;
    }


    public function byYear(int $year): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($year) {
            $qb->andWhere('l.year = :year')->setParameter('year', $year);
        };

        return $this;
    }


    public function orderByMonth(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->addOrderBy('l.month', $order);
        };

        return $this;
    }


    public function orderByCreationTime(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->addOrderBy('l.createdAt', $order);
        };

        return $this;
    }


    public function indexedById(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->indexBy('l', 'l.id');
        };

        return $this;
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        // todo
    }


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('l');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Listing::class, 'l');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}