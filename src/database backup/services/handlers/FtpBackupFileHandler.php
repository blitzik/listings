<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Kdyby\Monolog\Logger;
use Nette\SmartObject;

class FtpBackupFileHandler implements IBackupFileHandler
{
    use SmartObject;


    /** @var array */
    private $ftpCredentials;

    /** @var Logger */
    private $logger;


    public function __construct(
        array $ftpCredentials,
        Logger $logger
    ) {
        $this->ftpCredentials = $ftpCredentials;
        $this->logger = $logger;
    }


    /**
     * @param BackupFile $backupFile
     * @return BackupResultObject
     */
    public function process(BackupFile $backupFile): BackupResultObject
    {
        $d = $backupFile->getBackupDate();

        $resultObject = new BackupResultObject();
        foreach ($this->ftpCredentials as $credentials) {
            $backupPath = sprintf('%s/%s/%s', $credentials['path'], $d->format('Y'), $d->format('F'));
            $entireFilePath = sprintf('%s/%s', $backupPath, $backupFile->getFileName());
            try {
                $ftp = new \Ftp;
                $ftp->connect($credentials['host']);
                $ftp->login($credentials['username'], $credentials['password']);

                if (!$ftp->fileExists($backupPath)) {
                    $ftp->mkDirRecursive($backupPath);
                }

                $ftp->put($entireFilePath, $backupFile->getFilePath(), FTP_BINARY);
                $ftp->close();

            } catch (\FtpException $e) {
                $this->logger->addCritical(sprintf('Uploading backup file\'s failed. (%s) %s', $credentials['host'], $e));
                $resultObject->addError(sprintf('FTP upload to %s has failed', $credentials['host']));
            }
        }

        return $resultObject;
    }


}