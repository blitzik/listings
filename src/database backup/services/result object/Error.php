<?php

declare(strict_types=1);

namespace DatabaseBackup\Services;

use Nette\SmartObject;

class Error
{
    use SmartObject;


    /** @var string */
    private $message;


    public function __construct(string $message)
    {
        $this->message = $message;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}