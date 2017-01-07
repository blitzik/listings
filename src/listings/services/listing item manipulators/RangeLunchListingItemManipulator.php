<?php

declare(strict_types=1);

namespace Listings\Services;

use Listings\Services\Persisters\RangeLunchListingItemPersister;
use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Exceptions\Logic\InvalidStateException;
use Listings\LunchRangeListingItem;
use Kdyby\Doctrine\EntityManager;
use Listings\IListingItem;
use Nette\Utils\Arrays;
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
     * @param string $listingId
     * @return IListingItem[]
     */
    public function findListingItems(string $listingId): array
    {
        return $this->em->createQuery(
            'SELECT li FROM ' . LunchRangeListingItem::class . ' li INDEX BY li.day
             WHERE li.listing = :listing'
        )->setParameter('listing', hex2bin($listingId))
         ->getResult();
    }


    /**
     * @param int $day
     * @param string $listingId
     * @return IListingItem|null
     */
    public function getListingItemByDay(int $day, string $listingId)
    {
        return $this->em->createQuery(
            'SELECT li FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listing AND li.day = :day'
        )->setParameters(['listing' => hex2bin($listingId), 'day' => $day])
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


    /**
     * @param string $listingItemId
     * @return int
     */
    public function removeListingItem(string $listingItemId)
    {
        return $this->em->createQuery(
            'DELETE FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.id = :id'
        )->execute(['id' => hex2bin($listingItemId)]);
    }


    /**
     * @param string $listingId
     * @return array
     */
    public function loadLocalities(string $listingId): array
    {
        $localities = $this->em->createQuery(
            'SELECT DISTINCT li.locality FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listing'
        )->setParameter('listing', hex2bin($listingId))
         ->getArrayResult();

        return Arrays::flatten($localities);
    }


    /**
     * @param string $listingId
     * @param int $day
     * @return int
     */
    private function removeListingItemByDay(string $listingId, int $day): int
    {
        return $this->em->createQuery(
            'DELETE FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listingId AND li.day = :day'
        )->execute(['listingId' => hex2bin($listingId), 'day' => $day]);
    }


    /**
     * @param string $listingId
     * @return array
     */
    public function getWorkedDaysAndHours(string $listingId): array
    {
        $result = $this->em->createQuery(
            'SELECT COUNT(li.id) AS daysCount, SUM(li.workedHoursInSeconds) AS hoursInSeconds
             FROM ' . LunchRangeListingItem::class . ' li
             WHERE li.listing = :listing
             GROUP BY li.listing'
        )->setParameter('listing', hex2bin($listingId))
         ->getArrayResult();

        $listingData = [
            'daysCount' => empty($result) ? 0 : $result[0]['daysCount'],
            'hoursInSeconds' => empty($result) ? 0 : $result[0]['hoursInSeconds']
        ];

        return $listingData;
    }
}