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

namespace App\Exception\Handler;

use App\Exception\JsonException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Server\Exception\ServerException;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class JsonExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 判断被捕获到的异常是希望被捕获的异常
        if ($throwable instanceof ValidationException) {
            /** @var \Hyperf\Validation\ValidationException $throwable */
            $errMsg = $throwable->validator->errors()->first();
            // 格式化输出
            $data = json_encode([
                                    'status' => $throwable->getCode(),
                                    'message' => $errMsg,
                                ], JSON_UNESCAPED_UNICODE);
            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->withStatus(200)->withHeader("Content-Type","application/json")->withBody(new SwooleStream($data));
        }else if($throwable instanceof ServerException){
            // 格式化输出
            $data = json_encode([
                                    'status' => $throwable->getCode(),
                                    'message' => $throwable->getMessage(),
                                ], JSON_UNESCAPED_UNICODE);
            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->withStatus(200)->withHeader("Content-Type","application/json")->withBody(new SwooleStream($data));
        }
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
