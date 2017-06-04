<?php declare(strict_types=1);

use Listings\Utils\Time\ListingTime;
use Listings\Utils\TimeWithComma;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class ListingTimeTest extends \Tester\TestCase
{
    public function testSum()
    {
        $t1 = new ListingTime('01:00:00');
        $t2 = new ListingTime('01:00:00');

        $result = $t1->sum($t2);

        Assert::same('02:00:00', $result->getTime());
        Assert::same('7200', $result->getSeconds());
    }


    public function testSub()
    {
        $t1 = new ListingTime('01:00:00');
        $t2 = new ListingTime('01:00:00');

        $result = $t1->sub($t2);

        Assert::same('00:00:00', $result->getTime());
        Assert::same('0', $result->getSeconds());
    }


    public function testCompare()
    {
        $t1 = new ListingTime('01:30');
        $t2 = new ListingTime('01:30');

        $t3 = new ListingTime('02:00');

        Assert::same(0, $t1->compare($t2));
        Assert::same(1, $t3->compare($t2));
        Assert::same(-1, $t2->compare($t3));
    }


    public function testTimeWithComma()
    {
        Assert::exception(function () {
            $t1 = new ListingTime('1');
        }, \Listings\Exceptions\Logic\InvalidArgumentException::class,
        'Only positive numbers that are divisible by 1800 without reminder can pass');

        $t2 = new ListingTime(new TimeWithComma('1'));
        Assert::same('3600', $t2->getSeconds());

        $t3 = new ListingTime(new TimeWithComma('1,5'));
        Assert::same('5400', $t3->getSeconds());

        $t4 = new ListingTime('3600');
        Assert::same('1', $t4->getTimeWithComma());

        $t5 = new ListingTime('5400');
        Assert::same('1,5', $t5->getTimeWithComma());
    }


}


(new ListingTimeTest())->run();