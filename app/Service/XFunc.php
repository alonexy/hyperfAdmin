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

namespace App\Service;

trait xFunc
{
    public static function convert($num)
    {
        if ($num >= 100000) {
            $num = round($num / 10000) . 'W+';
        } elseif ($num >= 10000) {
            $num = round($num / 10000, 1) . 'W+';
        } elseif ($num >= 1000) {
            $num = round($num / 1000, 1) . 'K+';
        }
        return $num;
    }
}
