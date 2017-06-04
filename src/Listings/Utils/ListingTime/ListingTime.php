<?php declare(strict_types=1);

namespace Listings\Utils\Time;

use Listings\Exceptions\Runtime\NegativeResultOfTimeCalcException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Utils\TimeWithComma;
use blitzik\Utils\Time;
use Nette\SmartObject;

class ListingTime
{
    use SmartObject;


    const TIME_WITH_COMMA_REGEXP = '^\d+(,[05])?$';
    const TIME_REGEXP = '^\d+:[03]0:00$';
    const TIME_STEP = '1800'; // string

    /** @var Time */
    private $time;


    /**
     * There is different behaviour based on given value and its data type.
     *
     * NULL                   : sets object to 00:00:00
     * Time                   : sets object to Time time
     * ListingTime            : sets object to ListingTime time
     * Numeric int            : integer means number of seconds that must be
     *                          positive and divisible by 1800 without reminder.
     * DateTime               : object takes only the time part
     * String [e.g. 43:30:00] : sets this exact time
     * String [e.g. 43:30]    : hours and minutes time part
     * String [e.g 9 or 9,5]  : hours and minutes special format. ( but NOT 9,0)
     *
     * @param \DateTimeInterface|ListingTime|Time|int|string|null $time
     */
    public function __construct($time = null)
    {
        if ($time === null) {
            $time = '00:00:00';
        }

        $this->time = $this->gatherTime($time);
    }


    /**
     * @param \DateTimeInterface|ListingTime|Time|int|string|null $time
     * @return ListingTime
     */
    public function sum($time): ListingTime
    {
        $t = $this->gatherTime($time);

        return new ListingTime($this->time->sum($t));
    }


    /**
     * @param \DateTimeInterface|ListingTime|Time|int|string|null $time
     * @return ListingTime
     * @throws NegativeResultOfTimeCalcException
     */
    public function sub($time): ListingTime
    {
        $t = $this->gatherTime($time);

        $r = new ListingTime($this->time->sub($t));
        if ($r->compare('00:00:00') === -1) {
            throw new NegativeResultOfTimeCalcException;
        }

        return new self($r);
    }


    /**
     * @param \DateTimeInterface|ListingTime|int|string|null $time
     * @return int B = 1, L = -1, E = 0
     */
    public function compare($time): int
    {
        $pt = $this->gatherTime($time);

        return (int)bccomp($this->time->getSeconds(), $pt->getSeconds(), 0);
    }


    /**
     * @param \DateTimeInterface|ListingTime|Time|TimeWithComma|int|string|null $time
     * @return Time
     */
    private function gatherTime($time): Time
    {
        if ($time instanceof self) {
            $time = $time->getTime();

        } elseif ($time instanceof TimeWithComma) {
            $time = $this->timeWithComma2Time($time);
        }

        $time = new Time($time);
        if (bcmod($time->getSeconds(), self::TIME_STEP) !== '0' or bccomp($time->getSeconds(), '0', 0) === -1) {
            throw new InvalidArgumentException('Only positive numbers that are divisible by 1800 without reminder can pass');
        }

        return $time;
    }


    /**
     * @param TimeWithComma $timeWithComma
     * @return Time
     */
    private function timeWithComma2Time(TimeWithComma $timeWithComma): Time
    {
        $hours = str_replace(',', '.', $timeWithComma->getTimeWithComma());

        return new Time(bcmul('3600', $hours, 0));
    }


    public function getSeconds(): string
    {
        return $this->time->getSeconds();
    }


    public function getTime(): string
    {
        return $this->time->getTime();
    }


    public function getTimeWithComma(): string
    {
        $t = bcdiv($this->getSeconds(), '3600', 0);
        return str_replace('.', ',', $t);
    }


    public function __toString()
    {
        return $this->time->getTime();
    }
}