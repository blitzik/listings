<?php

declare(strict_types = 1);

namespace Listings\Facades;

use Kdyby\Doctrine\ResultSet;
use Listings\Queries\ListingItemQuery;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\EntityManager;
use Listings\ListingItem;
use Nette\SmartObject;

class ListingItemFacade
{
    use SmartObject;


    /** @var EntityRepository */
    private $listingItemRepository;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->listingItemRepository = $entityManager->getRepository(ListingItem::class);
    }


    /**
     * @param ListingItemQuery $query
     * @return ResultSet
     */
    public function findListingItems(ListingItemQuery $query): ResultSet
    {
        return $this->listingItemRepository->fetch($query);
    }
    
}