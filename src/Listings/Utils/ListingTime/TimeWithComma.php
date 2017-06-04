<?php declare(strict_types = 1);

namespace Listings\Utils;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Utils\Time\ListingTime;
use Nette\Utils\Validators;
use Nette\SmartObject;

final class TimeWithComma
{
    use SmartObject;


    /** @var string */
    private $timeWithComma;


    public function __construct(string $timeWithComma)
    {
        if (!Validators::is($timeWithComma, 'unicode') or !preg_match(sprintf('~%s~', ListingTime::TIME_WITH_COMMA_REGEXP), $timeWithComma)) {
            throw new InvalidArgumentException;
        }

        $this->timeWithComma = $timeWithComma;
    }


    public function getTimeWithComma(): string
    {
        return $this->timeWithComma;
    }
}