<?php declare(strict_types=1);

use blitzik\Utils\Time;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

class TimeTest extends \Tester\TestCase
{
    public function testNull()
    {
        $t = new Time();
        Assert::same('00:00:00', $t->getTime());
        Assert::same('0', $t->getSeconds());
    }


    public function testInt()
    {
        $t = new Time(9000);
        Assert::same('02:30:00', $t->getTime());
        Assert::same('9000', $t->getSeconds());


        $t2 = new Time(-9000);
        Assert::same('-02:30:00', $t2->getTime());
        Assert::same('-9000', $t2->getSeconds());
    }


    public function testNumericInt()
    {
        $t = new Time('9000');
        Assert::same('02:30:00', $t->getTime());
        Assert::same('9000', $t->getSeconds());


        $t2 = new Time('-9000');
        Assert::same('-02:30:00', $t2->getTime());
        Assert::same('-9000', $t2->getSeconds());
    }


    public function testDateTime()
    {
        $t = new Time(new DateTime('01:25:11'));
        Assert::same('01:25:11', $t->getTime());
        Assert::same('5111', $t->getSeconds());
    }


    public function testTime()
    {
        $t = new Time('01:25:11');
        Assert::same('01:25:11', $t->getTime());
        Assert::same('5111', $t->getSeconds());

        $t2 = new Time('1:25:11');
        Assert::same('01:25:11', $t2->getTime());
        Assert::same('5111', $t2->getSeconds());


        $t3 = new Time('-01:25:11');
        Assert::same('-01:25:11', $t3->getTime());
        Assert::same('-5111', $t3->getSeconds());

        $t4 = new Time('-1:25:11');
        Assert::same('-01:25:11', $t4->getTime());
        Assert::same('-5111', $t4->getSeconds());
    }


    public function testTimeObject()
    {
        $t = new Time(new Time('01:25:11'));
        Assert::same('01:25:11', $t->getTime());
        Assert::same('5111', $t->getSeconds());

        $t2 = new Time(new Time('1:25:11'));
        Assert::same('01:25:11', $t2->getTime());
        Assert::same('5111', $t2->getSeconds());
    }


    public function testHoursAndMinutes()
    {
        $t = new Time('01:30');
        Assert::same('01:30:00', $t->getTime());
        Assert::same('5400', $t->getSeconds());

        $t2 = new Time('1:30');
        Assert::same('01:30:00', $t2->getTime());
        Assert::same('5400', $t2->getSeconds());
    }


    // -----


    public function testSum()
    {
        $t1 = new Time('01:30');
        $t2 = new Time('-01:30');

        $result = $t1->sum($t2);

        Assert::same('00:00:00', $result->getTime());
        Assert::same('0', $result->getSeconds());
    }


    public function testSub()
    {
        $t1 = new Time('-01:30');
        $t2 = new Time('01:30');

        $result = $t1->sub($t2);

        Assert::same('-03:00:00', $result->getTime());
        Assert::same('-10800', $result->getSeconds());
    }


    public function testCompare()
    {
        $t1 = new Time('-01:30');
        $t2 = new Time('01:30');

        Assert::same(1, $t2->compare($t1));
        Assert::same(-1, $t1->compare($t2));

        $t3 = new Time('01:30');

        Assert::same(0, $t2->compare($t3));
    }


    public function testNegative()
    {
        $t1 = new Time('01:30');

        Assert::same('-01:30:00', $t1->getNegative()->getTime());

        $t2 = new Time('-01:30');

        Assert::same('01:30:00', $t2->getNegative()->getTime());
    }

    
}


(new TimeTest())->run();