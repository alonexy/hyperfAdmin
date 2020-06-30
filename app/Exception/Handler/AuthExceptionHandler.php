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

use http\Exception\BadMethodCallException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Server\Exception\ServerException;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\SimpleJwt\Exceptions\JWTException;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $errMsg = $throwable->getMessage();
        if ($throwable instanceof JWTException) {
            // 格式化输出
            $data = json_encode([
                                    'status' => 100032,
                                    'msg' => $errMsg,
                                ], JSON_UNESCAPED_UNICODE);
            $this->stopPropagation();
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->withBody(new SwooleStream($data));
        }
        if ($throwable instanceof UnauthorizedException) {
            $data = json_encode([
                                    'status' => 100033,
                                    'msg' => $errMsg,
                                ], JSON_UNESCAPED_UNICODE);
            $this->stopPropagation();
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json')->withBody(new SwooleStream($data));
        }
        if ($throwable instanceof AuthException) {
            $data = json_encode([
                                    'status' => 100034,
                                    'msg' => $errMsg,
                                ], JSON_UNESCAPED_UNICODE);
            $this->stopPropagation();
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json')->withBody(new SwooleStream($data));
        }
        // 交给下一个异常处理器
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof AuthException or $throwable instanceof JWTException;
    }
}
