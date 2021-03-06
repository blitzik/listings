<?php declare(strict_types=1);

namespace Listings\Facades;

use Listings\Exceptions\Runtime\EmployerNotFoundException;
use Listings\Services\Persisters\ListingPersister;
use Listings\Services\Removers\ListingRemover;
use Kdyby\Doctrine\EntityRepository;
use Listings\Queries\ListingQuery;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Listings\ListingSettings;
use Nette\SmartObject;
use Listings\Listing;
use Users\User;

final class ListingFacade
{
    use SmartObject;


    /** @var EntityRepository */
    private $listingRepository;

    /** @var ListingPersister */
    private $listingPersister;

    /** @var ListingRemover */
    private $listingRemover;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        ListingRemover $listingRemover,
        ListingPersister $listingPersister
    ) {
        $this->em = $entityManager;
        $this->listingRemover = $listingRemover;
        $this->listingPersister = $listingPersister;

        $this->listingRepository = $entityManager->getRepository(Listing::class);
    }


    /**
     * @param array $values
     * @param Listing|null $listing
     * @return Listing
     * @throws EmployerNotFoundException
     */
    public function save(array $values, Listing $listing = null): Listing
    {
        $values['setting'] = $this->getListingSettings($values['owner']);
        return $this->listingPersister->save($values, $listing);
    }


    public function remove(Listing $listing): void
    {
        $this->listingRemover->remove($listing);
    }


    public function getListing(ListingQuery $query): ?Listing
    {
        return $this->listingRepository->fetchOne($query);
    }


    public function findListings(ListingQuery $query): ResultSet
    {
        return $this->listingRepository->fetch($query);
    }


    public function getWorkedTime(int $userId): array
    {
        return $this->em->createQuery(
            'SELECT COUNT(l.id) AS numberOfListings, SUM(l.workedDays) AS workedDays, SUM(l.workedHours) AS workedHours
             FROM ' . Listing::class . ' l 
             WHERE l.owner = :ownerId'
        )->setParameter('ownerId', $userId)
         ->getArrayResult();
    }


    public function saveListingSettings(ListingSettings $settings): void
    {
        $this->em->flush($settings);
    }


    public function getListingSettings(User $user): ListingSettings
    {
        return $this->em->createQuery(
            'SELECT ls FROM ' . ListingSettings::class . ' ls
             WHERE ls.user = :usr'
        )->setParameter('usr', $user->getId())
         ->getOneOrNullResult();
    }

}