<?php declare(strict_types=1);

namespace Listings\Utils\Time;

use Listings\Exceptions\Logic\ListingTimeStepException;
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
     * TimeWithComma          : TimeWithComma object
     * String [e.g. 43:30:00] : sets this exact time
     * String [e.g. 43:30]    : hours and minutes time part
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
     */
    public function sub($time): ListingTime
    {
        $t = $this->gatherTime($time);

        return new self($this->time->sub($t));
    }


    /**
     * @param \DateTimeInterface|ListingTime|int|string|null $time
     * @return int B = 1, L = -1, E = 0
     */
    public function compare($time): int
    {
        $pt = $this->gatherTime($time);

        return (int)bccomp($this->getSeconds(), $pt->getSeconds(), 0);
    }


    public function isBiggerThan($time): bool
    {
        return $this->compare($time) === 1;
    }


    public function isBiggerOrEqualTo($time): bool
    {
        return $this->compare($time) >= 0;
    }


    public function isLowerThan($time): bool
    {
        return $this->compare($time) === -1;
    }


    public function isLowerOrEqualTo($time): bool
    {
        return $this->compare($time) <= 0;
    }


    public function isEqualTo($time): bool
    {
        return $this->compare($time) === 0;
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
        if (bcmod($time->getSeconds(), self::TIME_STEP) !== '0') {
            throw new ListingTimeStepException('Only numbers that are divisible by 1800 without reminder can pass');
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
        $t = bcdiv($this->getSeconds(), '3600', 1);

        return str_replace(',0', '', str_replace('.', ',', $t));
    }


    public function __toString()
    {
        return $this->time->getTime();
    }
}