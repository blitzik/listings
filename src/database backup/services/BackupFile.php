<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\SmartObject;
use Nette\Utils\Strings;

class BackupFile
{
    use SmartObject;


    /** @var string */
    private $storagePath;

    /** @var string */
    private $namePrefix;

    /** @var \DateTimeImmutable */
    private $createdAt;


    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
        $this->createdAt = new \DateTimeImmutable;
    }


    /**
     * @param string $namePrefix
     */
    public function setNamePrefix(string $namePrefix)
    {
        $this->namePrefix = Strings::webalize($namePrefix) . '-';
    }


    /**
     * @return string
     */
    public function getFileName(): string
    {
        return sprintf('%s%s', $this->namePrefix, $this->createdAt->format('Y-m-d-h-i-s'));
    }


    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->storagePath;
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getBackupDate(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}