<?php declare(strict_types = 1);

namespace Listings\ParameterFilters;

use blitzik\Router\ParameterFilters\IParameterFilter;
use Hashids\Hashids;

class ListingIdFilter implements IParameterFilter
{
    /** @var array */
    private $presenters = [];

    /** @var Hashids */
    private $hashIds;


    public function __construct()
    {
        $this->hashIds = new Hashids('w5aL', 5, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789');

        $this->presenters = [
            'Listings:Member:ListingDetail:default' => ['id'],
            'Listings:Member:Listing:remove' => ['id'],
            'Listings:Member:Listing:edit' => ['id'],
            'Listings:Member:ListingItem:default' => ['listingId'],
            'Listings:Member:ListingPdfGeneration:default' => ['id'],
        ];
    }


    public function getPresenters(): array
    {
        return $this->presenters;
    }


    public function getParameters(string $presenter): ?array
    {
        if (isset($this->presenters[$presenter])) {
            return $this->presenters[$presenter];
        }

        return null;
    }


    public function filterIn($modifiedParameter): string
    {
        $result = $this->hashIds->decode($modifiedParameter);
        if (count($result) === 0) {
            return (string)$modifiedParameter;
        }

        return (string)$result[0];
    }


    public function filterOut($parameter): string
    {
        return (string)$this->hashIds->encode($parameter);
    }

}