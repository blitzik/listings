<?php declare(strict_types=1);

namespace Listings\Services\Persisters;

use Listings\Exceptions\Runtime\EmployerNotFoundException;
use Listings\Facades\EmployerFacade;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Monolog\Logger;
use Nette\SmartObject;
use Listings\Listing;
use Users\User;

class ListingPersister
{
    use SmartObject;


    /** @var EmployerFacade */
    private $employerFacade;

    /** @var Logger */
    private $logger;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EmployerFacade $employerFacade,
        EntityManager $entityManager,
        Logger $logger
    ) {
        $this->em = $entityManager;
        $this->employerFacade = $employerFacade;
        $this->logger = $logger->channel('listing');
    }


    /**
     * @param array $values
     * @param Listing|null $listing
     * @return Listing
     * @throws EmployerNotFoundException
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
     * @throws EmployerNotFoundException
     */
    private function create(array $values): Listing
    {
        /** @var User $owner */
        $owner = $this->em->find(User::class, $values['owner']->getId());
        $listing = new Listing($owner, (int)$values['year'], (int)$values['month'], (int)$values['itemType']);
        if ($values['employer'] !== null) {
            $employer = $this->employerFacade->getEmployer($values['employer']);
            if ($employer === null) {
                throw new EmployerNotFoundException;
            }
            $listing->setEmployer($employer);
        }

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
     * @throws EmployerNotFoundException
     */
    private function update(array $values, Listing $listing): Listing
    {
        if ($values['employer'] !== null) {
            $employer = $this->employerFacade->getEmployer($values['employer']);
            if ($employer === null) {
                throw new EmployerNotFoundException;
            }
            $listing->setEmployer($employer);

        } else {
            $listing->setEmployer(null);
        }

        $listing->setName($values['name']);

        if (!empty($values['hourlyRate'])) {
            $listing->setHourlyRate($values['hourlyRate']);
        } else {
            $listing->setHourlyRate(null);
        }

        $this->em->flush();

        return $listing;
    }
}