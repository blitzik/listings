<?php declare(strict_types=1);

namespace Listings\Services;

use Listings\Services\Persisters\RangeLunchListingItemPersister;
use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Exceptions\Logic\InvalidStateException;
use Listings\LunchRangeListingItem;
use Kdyby\Doctrine\EntityManager;
use Listings\IListingItem;
use Nette\Utils\Arrays;
use blitzik\Utils\Time;
use Nette\SmartObject;
use Listings\Listing;

class RangeLunchListingItemManipulator implements IListingItemManipulator
{
    use SmartObject;


    /** @var RangeLunchListingItemPersister */
    private $rangeLunchListingItemPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        RangeLunchListingItemPersister $rangeLunchListingItemPersister
    ){
        $this->em = $entityManager;

        $this->rangeLunchListingItemPersister = $rangeLunchListingItemPersister;
    }


    /**
     * @param array $data
     * @param IListingItem|null $listingItem
     * @return IListingItem
     */
    public function save(array $data, IListingItem $listingItem = null): IListingItem
    {
        return $this->rangeLunchListingItemPersister->save($data, $listingItem);
    }


    /**
     * @param int $listingId
     * @return IListingItem[]
     */
    public function findListingItems(int $listingId): array
    {
        return $this->em->createQuery(
            'SELECT li FROM ' . LunchRangeListingItem::class . ' li INDEX BY li.day
             WHERE li.listing = :listing'
        )->setParameter('listing', $listingId)
         ->getResult();
    }


    public function getListingItemByDay(int $day, int $listingId): ?IListingItem
    {
        return $this->em->createQuery(
            'SELECT li FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listing AND li.day = :day'
        )->setParameters(['listing' => $listingId, 'day' => $day])
         ->getOneOrNullResult();
    }


    /**
     * @param IListingItem $listingItem
     * @return IListingItem
     * @throws ListingNotFoundException
     * @throws \Exception
     */
    public function copyDown(IListingItem $listingItem): IListingItem
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $listingItem->getMonth(), $listingItem->getYear());
        if (($listingItem->getDay() + 1) > $daysInMonth) {
            throw new InvalidStateException;
        }

        try {
            $this->em->beginTransaction();

            /** @var Listing $listing */
            $listing = $this->em->find(Listing::class, $listingItem->getListingId());
            if ($listing === null) {
                throw new ListingNotFoundException;
            }

            $this->removeListingItemByDay($listing->getId(), $listingItem->getDay() + 1);

            $newListingItem = new LunchRangeListingItem(
                $listing,
                $listingItem->getDay() + 1,
                $listingItem->getLocality(),
                $listingItem->getWorkStart(),
                $listingItem->getWorkEnd(),
                $listingItem->getLunchStart(),
                $listingItem->getLunchEnd()
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


    public function removeListingItem(int $listingItemId): void
    {
        try {
            $this->em->beginTransaction();

            /** @var LunchRangeListingItem|null $listingItem */
            $listingItem = $this->em->find(LunchRangeListingItem::class, $listingItemId);
            if ($listingItem !== null) {
                /** @var Listing $listing */
                $listing = $this->em->find(Listing::class, $listingItem->getListingId());
                $listing->updateWorkedDays(-1);
                $listing->updateWorkedHours((new Time($listingItem->getWorkedHours()->getSeconds()))->getNegative());
            }

            $this->em->remove($listingItem)->flush();

            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            throw  $e;
        }
    }


    public function loadLocalities(int $listingId): array
    {
        $localities = $this->em->createQuery(
            'SELECT DISTINCT li.locality FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listing'
        )->setParameter('listing', $listingId)
         ->getArrayResult();

        return Arrays::flatten($localities);
    }


    private function removeListingItemByDay(int $listingId, int $day): void
    {
        /** @var LunchRangeListingItem $listingItem */
        $listingItem = $this->em->createQuery(
            'SELECT li FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listingId AND li.day = :day'
        )->setParameters(['listingId' => $listingId, 'day' => $day])
         ->getOneOrNullResult();

        if ($listingItem === null) {
            return;
        }

        $this->removeListingItem($listingItem->getId());
    }

}