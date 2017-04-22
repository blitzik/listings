<?php declare(strict_types=1);

use Listings\Utils\Time\ListingTime;
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


}


(new ListingTimeTest())->run();