<?php declare(strict_types = 1);

use Listings\LunchRangeListingItem;
use Listings\Utils\TimeWithComma;
use blitzik\Authorization\Role;
use Listings\ListingItem;
use Listings\Listing;
use Tester\Assert;
use Users\User;

require __DIR__ . '/../bootstrap.php';

class ListingTest extends \Tester\TestCase
{
    public function testSimpleListingItem()
    {
        $role = new Role('role');
        $owner = new User('Lorem', 'Ipsum', 'lorem@ipsum.xy', 'abcde', $role);

        $listing = new Listing($owner, 2017, 1, Listing::ITEM_TYPE_LUNCH_SIMPLE);

        $item1 = new ListingItem($listing, 1, 'Consecteteur', '06:00', '16:00', new TimeWithComma('1'));
        $item2 = new ListingItem($listing, 2, 'Consecteteur', '06:00', '16:00', new TimeWithComma('1'));

        Assert::same('18:00:00', $listing->getWorkedHours()->getTime());
        Assert::same(2, $listing->getWorkedDays());
    }


    /*public function testLunchRangeListingItem() // todo
    {
        $role = new Role('role');
        $owner = new User('Lorem', 'Ipsum', 'lorem@ipsum.xy', 'abcde', $role);

        $listing = new Listing($owner, 2017, 1, Listing::ITEM_TYPE_LUNCH_RANGE);

        $item1 = new LunchRangeListingItem($listing, 1, 'Consecteteur', '06:00', '16:00', '11:00', '12:00');
        $item2 = new LunchRangeListingItem($listing, 2, 'Consecteteur', '06:00', '16:00', '11:00', '12:00');

        Assert::same('18:00:00', $listing->getWorkedHours()->getTime());
        Assert::same(2, $listing->getWorkedDays());
    }*/
}


(new ListingTest())->run();