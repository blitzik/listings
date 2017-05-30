<?php declare(strict_types=1);

namespace DatabaseBackup\Presenters;

use DatabaseBackup\Services\DatabaseBackup;
use Nette\Application\UI\Presenter;
use Nette\Caching\IStorage;
use Kdyby\Monolog\Logger;
use Nette\Caching\Cache;

final class CronBackupPresenter extends Presenter
{
    /** @var DatabaseBackup */
    private $databaseBackup;

    /** @var Logger */
    private $logger;


    /** @var string */
    private $password;

    /** @var Cache */
    private $cache;


    public function __construct($password, DatabaseBackup $databaseBackup, IStorage $storage, Logger $logger)
    {
        $this->password = $password;
        $this->databaseBackup = $databaseBackup;
        $this->cache = new Cache($storage, 'database-backup');
        $this->logger = $logger->channel('database-backup');
    }


    public function actionBackup($pwd)
    {
        if ($pwd !== $this->password) {
            $this->logger->info(sprintf('Unauthorized backup try. [%s]', $this->getHttpRequest()->getRemoteAddress()));
            $this->terminate();
        }

        if ($this->cache->load('database-backup') !== null) {
            $this->terminate();
        }

        try{
            $this->databaseBackup->backup('auto');

            $expDate = (new \DateTimeImmutable)->modify('tomorrow')->modify('-1 second')->getTimestamp();
            $this->cache->save('database-backup', true, [
                Cache::EXPIRE => $expDate
            ]);

        } catch (\Exception $e) {}

        $this->terminate();
    }
}