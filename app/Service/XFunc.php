<?php
/**
 * Created by PhpStorm.
 * User: alonexy
 * Date: 20/3/27
 * Time: 22:22
 */
namespace App\Service;

trait  xFunc{

    public static function convert($num)
    {
        if ($num >= 100000)
        {
            $num = round($num / 10000) .'W+';
        }
        else if ($num >= 10000)
        {
            $num = round($num / 10000, 1) .'W+';
        }
        else if($num >= 1000)
        {
            $num = round($num / 1000, 1) . 'K+';
        }

        return $num;
    }
}