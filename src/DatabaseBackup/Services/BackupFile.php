<?php declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\Utils\Strings;
use Nette\SmartObject;

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


    public function setNamePrefix(string $namePrefix): void
    {
        $this->namePrefix = Strings::webalize($namePrefix) . '-';
    }


    public function getFileName(): string
    {
        return sprintf('%s%s.sql', $this->namePrefix, $this->createdAt->format('Y-m-d-h-i-s'));
    }


    public function getFilePath(): string
    {
        return sprintf('%s%s', $this->storagePath, $this->getFileName());
    }


    public function getBackupDate(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}