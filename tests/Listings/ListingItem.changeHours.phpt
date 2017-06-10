<?php declare(strict_types = 1);

require __DIR__ . '/../bootstrap.php';

use Listings\Utils\TimeWithComma;
use blitzik\Authorization\Role;
use Listings\ListingItem;
use Listings\Listing;
use Tester\Assert;
use Users\User;


$role = new Role('member');
$owner = new User('Lorem', 'Ipsum', 'ipsum@app.abc', 'abcde', $role);

$listing = new Listing($owner, 2017, 6, Listing::ITEM_TYPE_LUNCH_SIMPLE);

$i = new ListingItem($listing, 1, 'Lorem ipsum', '06:00', '16:00', new TimeWithComma('1'));
Assert::same('09:00:00', $i->getWorkedHours()->getTime());

Assert::exception(function () use ($listing) {
    $i = new ListingItem($listing, 1, 'Lorem ipsum', '16:00', '06:00', new TimeWithComma('1'));
}, \Listings\Exceptions\Runtime\WorkedHoursRangeException::class);

Assert::exception(function () use ($listing) {
    $i = new ListingItem($listing, 1, 'Lorem ipsum', '06:00', '07:00', new TimeWithComma('1'));
}, \Listings\Exceptions\Runtime\WorkedHoursException::class);