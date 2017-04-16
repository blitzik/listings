<?php declare(strict_types=1);

namespace Listings\Services\Persisters;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Kdyby\Doctrine\EntityManager;
use Listings\ListingItem;
use Nette\SmartObject;

class SimpleLunchListingItemPersister
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @param array $values
     * @param ListingItem|null $listingItem
     * @return ListingItem
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function save(array $values, ListingItem $listingItem = null): ListingItem
    {
        if ($listingItem !== null) {
            $newListingItem = $this->update($values, $listingItem);
        } else {
            $newListingItem = $this->create($values);
        }

        return $newListingItem;
    }


    /**
     * @param array $values
     * @return ListingItem
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    private function create(array $values): ListingItem
    {
        $listingItem = new ListingItem(
            $values['listing'],
            $values['day'],
            $values['locality'],
            $values['workStart'],
            $values['workEnd'],
            $values['lunch']
        );
        $this->em->persist($listingItem)->flush();

        return $listingItem;
    }


    /**
     * @param array $values
     * @param ListingItem $listingItem
     * @return ListingItem
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    private function update(array $values, ListingItem $listingItem): ListingItem
    {
        $listingItem->changeLocality($values['locality']);
        $listingItem->changeHours($values['workStart'], $values['workEnd'], $values['lunch']);

        $this->em->flush();

        return $listingItem;
    }
}