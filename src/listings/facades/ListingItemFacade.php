<?php

declare(strict_types = 1);

namespace Listings\Facades;

use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Queries\Factories\ListingItemQueryFactory;
use Listings\Queries\ListingItemQuery;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Listings\ListingItem;
use Nette\SmartObject;
use Listings\Listing;

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
     * @return ListingItem|null
     */
    public function getListingItem(ListingItemQuery $query)
    {
        return $this->listingItemRepository->fetchOne($query);
    }


    /**
     * @param ListingItemQuery $query
     * @return ResultSet
     */
    public function findListingItems(ListingItemQuery $query): ResultSet
    {
        return $this->listingItemRepository->fetch($query);
    }


    /**
     * @param ListingItem $listingItem
     * @return ListingItem|void
     * @throws \Exception
     * @throws ListingNotFoundException
     */
    public function copyDown(ListingItem $listingItem)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $listingItem->getMonth(), $listingItem->getYear());
        if (($listingItem->getDay() + 1) > $daysInMonth) {
            return;
        }

        try {
            $this->em->beginTransaction();

            /** @var Listing $listing */
            $listing = $this->em->find(Listing::class, $listingItem->getListingId());
            if ($listing === null) {
                throw new ListingNotFoundException;
            }

            $this->removeListingItemByDay($listing->getId(), $listingItem->getDay() + 1);

            $newListingItem = new ListingItem(
                $listing,
                $listingItem->getDay() + 1,
                $listingItem->getLocality(),
                $listingItem->getWorkStart(),
                $listingItem->getWorkEnd(),
                $listingItem->getLunch()
            );

            $this->em->persist($newListingItem);

            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            throw $e;
        }

        return $newListingItem;
    }


    /**
     * @param string $listingItemId
     * @return int
     */
    public function removeListingItem(string $listingItemId): int
    {
        return $this->em->createQuery(
            'DELETE FROM ' . ListingItem::class . ' li
             WHERE li.id = :id'
        )->execute(['id' => $listingItemId]);
    }


    /**
     * @param string $listingId
     * @param int $day
     * @return int
     */
    public function removeListingItemByDay(string $listingId, int $day): int
    {
        return $this->em->createQuery(
            'DELETE FROM ' . ListingItem::class . ' li
             WHERE li.listing = :listingId AND li.day = :day'
        )->execute(['listingId' => $listingId, 'day' => $day]);
    }
    
}