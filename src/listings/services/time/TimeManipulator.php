<?php declare(strict_types=1);

namespace Listings\Services;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Nette\Utils\Validators;
use Nette\SmartObject;

final class TimeManipulator
{
    use SmartObject;

    const SECS_IN_MINUTE = '60'; // intentionally string
    const SECS_IN_HOUR = '3600'; // intentionally string

    /**
     * @param string $seconds numeric int
     * @return string
     */
    public static function seconds2time(string $seconds): string
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

    /**
     * @param string $time  HHH:MM:SS
     * @return string
     */
    public static function time2seconds(string $time): string
    {
        if (!self::isTimeFormatValid($time)) {
            throw new InvalidArgumentException(
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



    /**
     * @param mixed $time
     * @return bool
     */
    public static function isTimeFormatValid($time): bool
    {
        if (!preg_match('~^-?\d+:[0-5][0-9]:[0-5][0-9]$~', $time)) {
            return false;
        }

        return true;
    }
}