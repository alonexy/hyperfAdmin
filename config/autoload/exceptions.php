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

return [
    'handler' => [
        'http' => [
            App\Exception\Handler\AppExceptionHandler::class,
            App\Exception\Handler\JsonExceptionHandler::class, //不记录log
            App\Exception\Handler\JsonErrExceptionHandler::class, //记录log
            App\Exception\Handler\AuthExceptionHandler::class, //Api Auth
        ],
    ],
];
