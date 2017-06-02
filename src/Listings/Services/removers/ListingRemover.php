<?php declare(strict_types=1);

namespace Listings\Services\Removers;

use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;
use Listings\Listing;

class ListingRemover
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    public function remove(Listing $listing): void
    {
        $this->em->remove($listing);
        $this->em->flush();
    }
}