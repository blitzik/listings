<?php

declare(strict_types = 1);

namespace Listings\Services;

use Listings\Exceptions\Runtime\NegativeResultOfTimeCalcException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Nette\Utils\Validators;
use Nette\SmartObject;

class InvoiceTime
{
    use SmartObject;


    const TIME_WITH_COMMA_REGEXP = '^\d+(,[05])?$';
    const TIME_REGEXP = '^\d+:[03]0:00$';
    const TIME_STEP = '1800'; // string

    /** @var string */
    private $time;

    /** @var string */
    private $numberOfSeconds;


    /**
     * There is different behaviour based on given value and its data type.
     *
     * NULL                   : sets object to 00:00:00
     * InvoiceTime            : sets object to InvoiceTime time
     * Numeric int            : integer means number of seconds that must be
     *                          positive and divisible by 1800 without reminder.
     * DateTime               : object takes only the time part
     * String [e.g. 43:30:00] : sets this exact time
     * String [e.g. 43:30]    : hours and minutes time part
     * String [e.g 9 or 9,5]  : hours and minutes special format. ( but NOT 9,0)
     *
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     */
    public function __construct($time = null)
    {
        if ($time === null) {
            $time = '00:00:00';
        }

        $this->time = self::processTime($time);
    }


    /**
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     * @return string time in format HH..:MM:SS
     */
    public static function processTime($time): string
    {
        return self::gatherTime($time);
    }


    /**
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     * @return InvoiceTime
     */
    public function sum($time): InvoiceTime
    {
        $baseTime = self::gatherTime($time);
        $baseSecs = TimeManipulator::time2seconds($baseTime);
        $result = bcadd($baseSecs, $this->toSeconds(), 0);

        return new self($result);
    }


    /**
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     * @return InvoiceTime
     * @throws NegativeResultOfTimeCalcException
     */
    public function sub($time): InvoiceTime
    {
        $baseTime = self::gatherTime($time);

        $baseSecs = TimeManipulator::time2seconds($baseTime);
        $resultSecs = bcsub($this->toSeconds(), $baseSecs, 0);
        if (bccomp($resultSecs, '0', 0) === -1) { // $baseTime < 0
            throw new NegativeResultOfTimeCalcException;
        }

        return new self($resultSecs);
    }


    /**
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     * @return int B = 1, L = -1, E = 0
     */
    public function compare($time): int
    {
        $paramSecs = TimeManipulator::time2seconds(self::gatherTime($time));
        $objSecs = $this->toSeconds();

        if (bccomp($objSecs, $paramSecs, 0) === 1) {
            return 1;

        } elseif (bccomp($objSecs, $paramSecs, 0) === -1) {
            return -1;

        } else {
            return 0;
        }
    }


    /**
     * @param \DateTimeInterface|InvoiceTime|int|string|null $time
     * @return string
     */
    private static function gatherTime($time): string
    {
        do {
            if ($time instanceof self) {
                $time = $time->getTime();
                break;
            }

            if ($time instanceof \DateTimeInterface) {
                $time = $time->format('H:i:s');
                break;
            }

            // time in seconds
            if (Validators::is($time, 'numericint:') and bcmod((string)$time, self::TIME_STEP) === '0') {
                if (bccomp((string)$time, '0', 0) === -1) {
                    throw new InvalidArgumentException('Only positive numbers that are divisible by 1800 without reminder can pass');
                }
                $time = TimeManipulator::seconds2time((string)$time);
                break;
            }

            // time in hours:minutes format
            if (Validators::is($time, 'unicode') and preg_match('~^\d+:[03]0$~', $time)) {
                $time = $time . ':00'; // add SECONDS part to HH..:MM format
                break;
            }

            // time in format with comma
            if (Validators::is($time, 'unicode') and preg_match(sprintf('~%s~', self::TIME_WITH_COMMA_REGEXP), $time)) {
                $time = self::timeWithComma2Time($time);
                break;
            }
        } while (false);

        if (!preg_match(sprintf('~%s~', self::TIME_REGEXP), $time)) {
            throw new InvalidArgumentException(
                'Wrong $time format.'
            );
        }

        return $time;
    }


    /**
     * @param $timeWithComma
     * @return string
     */
    private static function timeWithComma2Time(string $timeWithComma): string
    {
        $hours = str_replace(',', '.', $timeWithComma);

        return TimeManipulator::seconds2time(bcmul(TimeManipulator::SECS_IN_HOUR, $hours, 0));
    }


    /**
     * @return string
     */
    public function toSeconds(): string
    {
        if (!isset($this->numberOfSeconds)) {
            $this->numberOfSeconds = TimeManipulator::time2seconds($this->time);
        }

        return $this->numberOfSeconds;
    }


    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }


    public function __toString()
    {
        return $this->time;
    }
}