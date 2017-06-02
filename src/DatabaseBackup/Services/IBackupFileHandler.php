<?php declare(strict_types=1);

namespace DatabaseBackup\Services;

interface IBackupFileHandler
{
    public function process(BackupFile $backupFile): BackupResultObject;
}