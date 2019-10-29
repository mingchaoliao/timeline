<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 10/29/19
 * Time: 3:59 PM
 */

namespace App\Timeline\App;


use App\Timeline\Domain\Repositories\SearchEventRepository;
use Elasticsearch\Client;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Output\BufferedOutput;

class HealthCheckService
{
    /**
     * @var Client
     */
    private $es;
    /**
     * @var SearchEventRepository
     */
    private $eventRepository;
    /**
     * @var ConnectionInterface|\Illuminate\Database\Connection
     */
    private $dbConn;
    /**
     * @var Kernel
     */
    private $artisan;

    /**
     * HealthCheckService constructor.
     * @param Client $es
     * @param SearchEventRepository $eventRepository
     * @param \Illuminate\Database\Connection|ConnectionInterface $dbConn
     * @param Kernel $artisan
     */
    public function __construct(Client $es, SearchEventRepository $eventRepository, $dbConn, Kernel $artisan)
    {
        $this->es = $es;
        $this->eventRepository = $eventRepository;
        $this->dbConn = $dbConn;
        $this->artisan = $artisan;
    }

    public function isDBConnected(): bool
    {
        try {
            $this->dbConn->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isDBSetupCompleted(): bool
    {
        try {
            $output = new BufferedOutput();
            $this->artisan->call('migrate:status', [], $output);
            $str = $output->fetch();
            $parts = explode("\n", $str);
            if (count($parts) < 2 || strpos($parts[1], '| Ran?') !== 0) {
                return false;
            }

            if (count(array_filter($parts, function (string $str) {
                    if (strpos($str, '| No') === 0) {
                        return true;
                    }
                    return false;
                })) > 0) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isQueueConnected(): bool
    {
        try {
            Redis::connection()->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isSearchEngineConnected(): bool
    {
        return $this->es->ping();
    }

    public function isSearchEngineIndexCreated(): bool
    {
        return $this->eventRepository->hasEventIndex();
    }
}