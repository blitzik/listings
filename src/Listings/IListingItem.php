<?php declare(strict_types=1);

namespace Listings;

use Listings\Services\InvoiceTime;

interface IListingItem
{
    /**
     * @return int
     */
    public function getListingItemsType(): int;


    /**
     * @return int
     */
    public function getDay(): int;


    /**
     * @return int
     */
    public function getMonth(): int;


    /**
     * @return int
     */
    public function getYear(): int;


    /**
     * @return InvoiceTime
     */
    public function getWorkStart(): InvoiceTime;


    /**
     * @return InvoiceTime
     */
    public function getWorkEnd(): InvoiceTime;


    /**
     * @return InvoiceTime
     */
    public function getLunch(): InvoiceTime;


    /**
     * @return InvoiceTime
     */
    public function getWorkedHours(): InvoiceTime;


    /**
     * @return InvoiceTime
     */
    public function getWorkedHoursWithLunch(): InvoiceTime;


    /**
     * @return string
     */
    public function getLocality(): string;
}