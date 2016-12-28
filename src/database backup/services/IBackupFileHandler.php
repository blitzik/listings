<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

interface IBackupFileHandler
{
    /**
     * @param BackupFile $backupFile
     * @return BackupResultObject
     */
    public function process(BackupFile $backupFile): BackupResultObject;
}