<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Process;

use Hyperf\AsyncQueue\Process\ConsumerProcess;
use Hyperf\Process\Annotation\Process;
use Psr\Container\ContainerInterface;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Contract\StdoutLoggerInterface;
/**
 * @Process(name="async-queue")
 */
class AsyncQueueConsumer extends ConsumerProcess
{
    /**
     * @var string
     */
    protected $queue = 'dwz_asyn_job';

    /**
     * @var DriverInterface
     */
    protected $driver;
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $factory = $this->container->get(DriverFactory::class);
        $this->driver = $factory->get($this->queue);
        $this->config = $factory->getConfig($this->queue);

        $this->name = "queue.{$this->queue}";
        $this->nums = $this->config['processes'] ?? 1;
    }

    public function handle(): void
    {
        if (! $this->driver instanceof DriverInterface) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->critical(sprintf('[CRITICAL] process %s is not work as expected, please check the config in [%s]', ConsumerProcess::class, 'config/autoload/queue.php'));
            return;
        }

        $this->driver->consume();
    }
}
