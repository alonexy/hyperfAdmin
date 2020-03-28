<?php
declare(strict_types=1);

namespace App\Service;

use App\Job\DwzJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class DwzQueueService
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('dwz_asyn_job');
    }

    /**
     * 生产消息.
     * @param $params 数据
     * @param int $delay 延时时间 单位秒
     */
    public function push($params, int $delay = 0): bool
    {
        return $this->driver->push(new DwzJob($params), $delay);
    }
}