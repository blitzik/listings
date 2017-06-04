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
use blitzik\Utils\Time;
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


    public function getListingItemByDay(int $day, string $listingId): ?IListingItem
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
                $listingItem->getLunch()->getTimeWithComma()
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
     * @return void
     * @throws \Exception
     */
    public function removeListingItem(string $listingItemId): void
    {
        try {
            $this->em->beginTransaction();

            /** @var ListingItem|null $listingItem */
            $listingItem = $this->em->find(ListingItem::class, $listingItemId);
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


    public function loadLocalities(string $listingId): array
    {
        $localities = $this->em->createQuery(
            'SELECT DISTINCT li.locality FROM ' . ListingItem::class . ' li
             WHERE li.listing = :listing'
        )->setParameter('listing', hex2bin($listingId))
            ->getArrayResult();

        return Arrays::flatten($localities);
    }


    private function removeListingItemByDay(string $listingId, int $day): void
    {
        /** @var ListingItem $listingItem */
        $listingItem = $this->em->createQuery(
            'SELECT li FROM ' . ListingItem::class . ' li
             WHERE li.listing = :listingId AND li.day = :day'
        )->setParameters(['listingId' => hex2bin($listingId), 'day' => $day])
         ->getOneOrNullResult();

        if ($listingItem === null) {
            return;
        }

        $this->removeListingItem($listingItem->getId());
    }
}