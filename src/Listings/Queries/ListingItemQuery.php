<?php declare(strict_types=1);

namespace Listings\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Listings\ListingItem;
use Kdyby;

class ListingItemQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    /**
     * @param string $listingId
     * @return ListingItemQuery
     */
    public function byListingId(string $listingId): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($listingId) {
            $qb->andWhere('li.listing = :listingId')
               ->setParameter('listingId', hex2bin($listingId));
        };

        return $this;
    }


    /**
     * @param int $day
     * @return ListingItemQuery
     */
    public function byDay(int $day): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($day) {
            $qb->andWhere('li.day = :day')
               ->setParameter('day', $day);
        };

        return $this;
    }


    /**
     * @return ListingItemQuery
     */
    public function indexedByDay(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->indexBy('li', 'li.day');
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
        $qb->select('li');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository): Kdyby\Doctrine\QueryBuilder
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(ListingItem::class, 'li');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}