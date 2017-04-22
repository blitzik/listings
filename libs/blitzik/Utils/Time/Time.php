<?php declare(strict_types=1);

namespace blitzik\Utils;

use Nette\Utils\Validators;

class Time
{
    const SECS_IN_MINUTE = '60'; // intentionally string
    const SECS_IN_HOUR = '3600'; // intentionally string


    /** @var string */
    private $time;

    /** @var string */
    private $numberOfSeconds;


    /**
     * There is different behaviour based on given value and its data type.
     *
     * NULL                   : sets object to 00:00:00
     * Time                   : sets object to ListingTime time
     * Numeric int            : integer means number of seconds that must be
     *                          positive and divisible by 1800 without reminder.
     * DateTime               : object takes only the time part
     * String [e.g. 43:30:00] : sets this exact time
     * String [e.g. 43:30]    : hours and minutes time part
     *
     * @param \DateTimeInterface|Time|int|string|null $time
     */
    public function __construct($time = null)
    {
        if ($time === null) {
            $time = '00:00:00';
        }

        $this->time = $this->gatherTime($time);
    }


    /**
     * @param \DateTimeInterface|Time|int|string|null $time
     * @return Time
     */
    public function sum($time): Time
    {
        $baseTime = $this->gatherTime($time);
        $baseSecs = $this->time2seconds($baseTime);
        $result = bcadd($baseSecs, $this->getSeconds(), 0);

        return new self($result);
    }


    /**
     * @param \DateTimeInterface|Time|int|string|null $time
     * @return Time
     */
    public function sub($time): Time
    {
        $baseTime = $this->gatherTime($time);

        $baseSecs = $this->time2seconds($baseTime);
        $resultSecs = bcsub($this->getSeconds(), $baseSecs, 0);

        return new self($resultSecs);
    }


    /**
     * @param \DateTimeInterface|Time|int|string|null $time
     * @return int B = 1, L = -1, E = 0
     */
    public function compare($time): int
    {
        $paramSecs = $this->time2seconds($this->gatherTime($time));
        $objSecs = $this->getSeconds();

        return (int)bccomp($objSecs, $paramSecs, 0);
    }


    /**
     * @param \DateTimeInterface|Time|int|string|null $time
     * @return string
     */
    private function gatherTime($time): string
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
            if (Validators::is($time, 'numericint')) {
                $this->numberOfSeconds = (string)$time;
                $time = $this->seconds2time((string)$time);
                break;
            }

            // time in hours:minutes format
            if (Validators::is($time, 'unicode') and preg_match('~^-?\d+:[0-5][0-9]$~', $time)) {
                $time = $time . ':00'; // add SECONDS part to HH..:MM format
                break;
            }
        } while (false);

        if (!$this->isTimeFormatValid($time)) {
            throw new \InvalidArgumentException(
                'Wrong $time format.'
            );
        }

        return $this->processTime($time);
    }


    private function processTime(string $time): string
    {
        $sign = mb_strpos($time, '-') !== false ? '-' : '';
        $t = str_replace('-', '', $time);

        $tp = explode(':', $t);

        return sprintf('%s%02d:%s:%s', $sign, $tp[0], $tp[1], $tp[2]);
    }


    /**
     * @return string
     */
    public function getSeconds(): string
    {
        if (!isset($this->numberOfSeconds)) {
            $this->numberOfSeconds = $this->time2seconds($this->time);
        }

        return $this->numberOfSeconds;
    }


    public function isNegative(): bool
    {
        return bccomp($this->getSeconds(), '0', 0) < 0;
    }


    public function getNegative(): Time
    {
        $result = bcmul($this->getSeconds(), '-1', 0);

        return new Time($result);
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


    // -----


    public static function isTimeFormatValid(string $time): bool
    {
        if (!preg_match('~^-?\d+:[0-5][0-9]:[0-5][0-9]$~', $time)) {
            return false;
        }

        return true;
    }


    private function seconds2time(string $seconds): string
    {
        Validators::assert($seconds, 'numericint');
        $sign = strpos($seconds, '-') !== false ? '-' : '';
        $s = str_replace('-', '', $seconds);

        return sprintf(
            '%s%02d:%02d:%02d',
            $sign,
            (bcdiv($s, self::SECS_IN_HOUR, 2)),
            bcmod(bcdiv($s, self::SECS_IN_MINUTE, 2), self::SECS_IN_MINUTE),
            bcmod($s, self::SECS_IN_MINUTE)
        );
    }


    private function time2seconds(string $time): string
    {
        if (!self::isTimeFormatValid($time)) {
            throw new \InvalidArgumentException(
                'Argument $time has wrong format. ' . '"'.$time.'" given.'
            );
        }

        $sign = strpos($time, '-') !== false ? '-1' : '1';
        $t = str_replace('-', '', $time);
        list($hours, $minutes, $seconds) = sscanf($t, '%d:%d:%d');

        $hoursInSeconds = bcmul((string)$hours, self::SECS_IN_HOUR, 0);
        $minutesInSeconds = bcmul((string)$minutes, self::SECS_IN_MINUTE, 0);

        $totalSeconds = bcadd(bcadd($hoursInSeconds, $minutesInSeconds, 0), (string)$seconds, 0);
        return bcmul($totalSeconds, $sign, 0);
    }
}