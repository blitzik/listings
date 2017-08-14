<?php declare(strict_types=1);

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


    public function byId(int $id): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('l.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    public function byPresKey(string $presKey): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($presKey) {
            $qb->andWhere('l.presKey = :key')->setParameter('key', hex2bin($presKey));
        };

        return $this;
    }


    public function withOwner(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->addSelect('o')
               ->leftJoin('l.owner', 'o');
        };

        return $this;
    }


    public function withEmployer(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->addSelect('e')
               ->leftJoin('l.employer', 'e');
        };

        return $this;
    }


    public function withSettings(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->addSelect('s')
               ->leftJoin('l.defaultSettings', 's');
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
            $qb->andWhere('l.owner = :owner')->setParameter('owner', $owner->getId());
        };

        return $this;
    }


    public function byOwnerId(int $owner): self
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


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository): Kdyby\Doctrine\QueryBuilder
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('l');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository): Kdyby\Doctrine\QueryBuilder
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