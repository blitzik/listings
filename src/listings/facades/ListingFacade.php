<?php

declare(strict_types = 1);

namespace Listings\Facades;

use Listings\Services\Persisters\ListingPersister;
use Kdyby\Doctrine\EntityRepository;
use Listings\Queries\ListingQuery;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Nette\SmartObject;
use Listings\Listing;

final class ListingFacade
{
    use SmartObject;


    /** @var EntityRepository */
    private $listingRepository;

    /** @var ListingPersister */
    private $listingPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        ListingPersister $listingPersister
    ) {
        $this->em = $entityManager;
        $this->listingPersister = $listingPersister;

        $this->listingRepository = $entityManager->getRepository(Listing::class);
    }


    /**
     * @param array $values
     * @param Listing|null $listing
     * @return Listing
     */
    public function save(array $values, Listing $listing = null): Listing
    {
        return $this->listingPersister->save($values, $listing);
    }


    /**
     * @param ListingQuery $query
     * @return Listing|null
     */
    public function getListing(ListingQuery $query)
    {
        return $this->listingRepository->fetchOne($query);
    }


    /**
     * @param ListingQuery $query
     * @return ResultSet
     */
    public function findListings(ListingQuery $query): ResultSet
    {
        return $this->listingRepository->fetch($query);
    }

}