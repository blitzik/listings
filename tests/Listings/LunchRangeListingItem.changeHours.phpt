<?php declare(strict_types = 1);

require __DIR__ . '/../bootstrap.php';

use Listings\Utils\Time\ListingTime;
use Listings\LunchRangeListingItem;
use blitzik\Authorization\Role;
use Listings\ListingSettings;
use Listings\Listing;
use Tester\Assert;
use Users\User;


$role = new Role('member');
$owner = new User('Lorem', 'Ipsum', 'ipsum@app.abc', 'abcde', $role);
$settings = new ListingSettings(
    $owner, Listing::ITEM_TYPE_LUNCH_SIMPLE,
    new ListingTime('06:00'), new ListingTime('14:30'),
    new ListingTime('10:00'), new ListingTime('10:30')
);
$listing = new Listing($owner, $settings, 2017, 6, Listing::ITEM_TYPE_LUNCH_RANGE);

$i1 = new LunchRangeListingItem($listing, 1, 'Lorem ipsum', '06:00', '16:00', '10:00', '11:00');
Assert::same('09:00:00', $i1->getWorkedHours()->getTime());

$i2 = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '06:00', '16:00', '06:00', '15:30');
Assert::same('00:30:00', $i2->getWorkedHours()->getTime());

Assert::exception(function () use ($listing) { // at least 30 minutes of work must be done
    $i = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '06:00', '16:00', '06:00', '16:00');
}, \Listings\Exceptions\Runtime\WorkedHoursException::class);

Assert::exception(function () use ($listing) { // workStart > workEnd
    $i = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '16:00', '06:00', '10:00', '11:00');
}, \Listings\Exceptions\Runtime\WorkedHoursRangeException::class);

Assert::exception(function () use ($listing) { // lunchStart > lunchEnd
    $i = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '06:00', '16:00', '10:00', '09:00');
}, \Listings\Exceptions\Runtime\LunchHoursRangeException::class);

Assert::exception(function () use ($listing) { // lunchStart < workStart
    $i = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '06:00', '16:00', '05:00', '11:00');
}, \Listings\Exceptions\Runtime\LunchHoursException::class);

Assert::exception(function () use ($listing) { // lunchEnd > workEnd
    $i = new LunchRangeListingItem($listing, 2, 'Lorem ipsum', '06:00', '16:00', '10:00', '17:00');
}, \Listings\Exceptions\Runtime\LunchHoursException::class);

$i3 = new LunchRangeListingItem($listing, 3, 'Lorem ipsum', '06:00', '16:00', '00:00', '00:00');
Assert::same('10:00:00', $i3->getWorkedHours()->getTime());



