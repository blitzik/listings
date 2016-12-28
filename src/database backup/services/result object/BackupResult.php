<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\SmartObject;

class BackupResult
{
    use SmartObject;


    /** @var BackupResultObject[] */
    private $resultObjects = [];


    /**
     * @param BackupResultObject $resultObject
     */
    public function add(BackupResultObject $resultObject)
    {
        $this->resultObjects[] = $resultObject;
    }


    public function hasErrors()
    {
        foreach ($this->resultObjects as $resultObject) {
            if ($resultObject->hasErrors()) {
                return true;
            }
        }

        return false;
    }


    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->resultObjects as $resultObject) {
            $errors = array_merge($errors, $resultObject->getErrors());
        }

        return $errors;
    }
}