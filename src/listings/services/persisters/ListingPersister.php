<?php

declare(strict_types = 1);

namespace Listings\Services\Persisters;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Monolog\Logger;
use Listings\Listing;
use Nette\SmartObject;
use Users\User;

class ListingPersister
{
    use SmartObject;


    /** @var Logger */
    private $logger;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger->channel('listing');
    }


    /**
     * @param array $values
     * @param Listing|null $listing
     * @return Listing
     * @throws \Exception
     */
    public function save(array $values, Listing $listing = null): Listing
    {
        try {
            if (isset($listing) and $listing->getId() !== null) {
                $newListing = $this->update($values, $listing);
            } else {
                $newListing = $this->create($values);
            }

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            $this->logger->addCritical(sprintf('Listing cannot be saved. Exception code: %s; Exception message: ', $e->getCode(), $e->getMessage()));

            throw $e;
        }

        return $newListing;
    }


    /**
     * @param array $values
     * @return Listing
     */
    private function create(array $values): Listing
    {
        $owner = $this->em->find(User::class, $values['owner']->getId());
        $listing = new Listing($owner, $values['year'], $values['month']);
        if (isset($values['name'])) {
            $listing->setName($values['name']);
        }

        if (isset($values['hourlyRate'])) {
            $listing->setHourlyRate($values['hourlyRate']);
        }

        $this->em->persist($listing)->flush();

        return $listing;
    }


    /**
     * @param array $values
     * @param Listing $listing
     * @return Listing
     */
    private function update(array $values, Listing $listing): Listing
    {
        if (isset($values['name'])) {
            $listing->setName($values['name']);
        }

        if (isset($values['hourlyRate'])) {
            $listing->setHourlyRate($values['hourlyRate']);
        }

        $this->em->flush();

        return $listing;
    }
}