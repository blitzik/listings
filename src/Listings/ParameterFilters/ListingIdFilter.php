<?php declare(strict_types = 1);

namespace Listings\ParameterFilters;

use blitzik\Router\ParameterFilters\IParameterFilter;
use Hashids\Hashids;

class ListingIdFilter implements IParameterFilter
{
    /** @var Hashids */
    private $hashIds;


    public function __construct()
    {
        $this->hashIds = new Hashids('w5aL', 5, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789');
    }


    public function getName(): string
    {
        return 'ListingIdFilter';
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