<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\SmartObject;

class BackupResultObject
{
    use SmartObject;


    /** @var Error[] */
    private $errors = [];


    public function addError(string $message)
    {
        $this->errors[] = new Error($message);
    }


    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}