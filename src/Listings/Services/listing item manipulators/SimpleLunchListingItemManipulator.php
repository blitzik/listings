<?php declare(strict_types=1);

namespace Listings\Services;

use Listings\Services\Persisters\SimpleLunchListingItemPersister;
use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Queries\Factories\ListingItemQueryFactory;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Listings\Exceptions\Logic\InvalidStateException;
use Kdyby\Doctrine\EntityManager;
use Listings\IListingItem;
use Listings\ListingItem;
use Nette\Utils\Arrays;
use Nette\SmartObject;
use Listings\Listing;

class SimpleLunchListingItemManipulator implements IListingItemManipulator
{
    use SmartObject;


    /** @var SimpleLunchListingItemPersister */
    private $simpleLunchListingItemPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        SimpleLunchListingItemPersister $simpleLunchListingItemPersister
    ){
        $this->em = $entityManager;
        $this->simpleLunchListingItemPersister = $simpleLunchListingItemPersister;
    }


    /**
     * @param array $data
     * @param IListingItem|null $listingItem
     * @return IListingItem
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function save(array $data, IListingItem $listingItem = null): IListingItem
    {
        return $this->simpleLunchListingItemPersister->save($data, $listingItem);
    }


    /**
     * @param string $listingId
     * @return IListingItem[]
     */
    public function findListingItems(string $listingId): array
    {
        return $this->em->createQuery(
            'SELECT li FROM ' . ListingItem::class . ' li INDEX BY li.day
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
        return $this->em
                    ->getRepository(ListingItem::class)
                    ->fetchOne(
                        ListingItemQueryFactory::filterByListingAndDay($listingId, $day)
                        ->indexedByDay()
                    );
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
    public function removeListingItem(string $listingItemId)
    {
        return $this->em->createQuery(
            'DELETE FROM ' . ListingItem::class . ' li
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
            'SELECT DISTINCT li.locality FROM ' . ListingItem::class . ' li
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
            'DELETE FROM ' . ListingItem::class . ' li
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
             FROM ' . ListingItem::class . ' li
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