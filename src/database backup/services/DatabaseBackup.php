<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\Utils\FileSystem;
use Kdyby\Monolog\Logger;
use Nette\IOException;
use Nette\SmartObject;

class DatabaseBackup
{
    use SmartObject;


    public $onSuccessfulDatabaseBackup;
    public $onDatabaseBackupFailure;


    /** @var \MySQLDump */
    private $mysqlDump;

    /** @var Logger */
    private $logger;


    /** @var string */
    private $backupTempPath;

    /** @var IBackupFileHandler[] */
    private $handlers = [];


    public function __construct(
        array $databaseCredentials,
        string $backupTempPath,
        Logger $logger
    ) {
        $this->backupTempPath = $backupTempPath;
        $this->logger = $logger;

        $this->mysqlDump = new \MySQLDump(
            new \mysqli(
                $databaseCredentials['host'],
                $databaseCredentials['user'],
                $databaseCredentials['password'],
                $databaseCredentials['dbname']
            )
        );
    }


    /**
     * @param IBackupFileHandler $backupHandler
     */
    public function addHandler(IBackupFileHandler $backupHandler)
    {
        $this->handlers[] = $backupHandler;
    }


    /**
     * @param string|null $filenamePrefix
     * @throws \Exception
     */
    public function backup(string $filenamePrefix = null)
    {
        $storagePath = $this->prepareStoragePath($this->backupTempPath);

        $file = new BackupFile($storagePath);
        if ($filenamePrefix !== null) {
            $file->setNamePrefix($filenamePrefix);
        }

        try {
            $this->mysqlDump->save($file->getFilePath());

        } catch (\Exception $e) {
            $this->onDatabaseBackupFailure();

            throw $e;
        }

        $result = new BackupResult();
        foreach ($this->handlers as $handler) {
            $result->add($handler->process($file));
        }

        $this->onSuccessfulDatabaseBackup($file, $result);

        //$this->removeBackupFile($file);
    }


    /**
     * @param $path
     * @return string
     * @throws IOException
     */
    private function prepareStoragePath($path): string
    {
        if (!is_dir($path)) {
            try {
                FileSystem::createDir($path);
            } catch (IOException $e) {
                $this->logger->addCritical(sprintf('DIR creation failure: %s', $e));

                throw $e;
            }
        }

        return $path;
    }


    private function removeBackupFile(BackupFile $file)
    {
        if (file_exists($file->getFilePath()) and !is_dir($file->getFilePath())) {
            FileSystem::delete($file->getFilePath());
        }
    }
}