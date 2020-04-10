<?php
/**
 * Created by PhpStorm.
 * User: alonexy
 * Date: 20/3/27
 * Time: 22:41
 */

namespace App\Service;

use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class DwzService
{
    protected $redis;

    const DWZ_INCR_ID_KEY = "dwz:_id";
    const DWZ_URL_LIST_KEY = "dwz:_list";

    const DWZ_URL_UNIQUE_SET_KEY = "dwz:_unique";
    const DWZ_URL_UNIQUE_SET_LIST_KEY = "dwz:_unique_list";

    const DWZ_ACCESS_NUM_STAT_KEY = "dwz:stat:_access_num";
    const DWZ_ACCESS_NUM_DAY_STAT_KEY = "dwz:stat:_access_day_num:";
    const DWZ_ACCESS_NUM_IP_STAT_KEY = "dwz:stat:_ip_num";
    const DWZ_ACCESS_NUM_IP_DAY_STAT_KEY = "dwz:stat:_ip_day_num:";

    public function __construct()
    {
        $container   = ApplicationContext::getContainer();
        $this->redis = $container->get(RedisFactory::class)->get('default');
    }

    public function GetIncrId()
    {
        return $this->redis->incr(self::DWZ_INCR_ID_KEY, 1);
    }

    public function GetNowId()
    {
        return $this->redis->get(self::DWZ_INCR_ID_KEY);
    }

    //设置url是否已存在并获取唯一key
    public function IsUniqueUrl($uri) : array
    {
        $md5Key = md5($uri);
        $res    = $this->redis->sadd(self::DWZ_URL_UNIQUE_SET_KEY, $md5Key);
        return [$res, $md5Key];
    }

    //设置唯一key对应的S4id
    public function SetUrlUniqueListByKey($md5Key, $s4Id)
    {
        return $this->redis->hset(self::DWZ_URL_UNIQUE_SET_LIST_KEY, "{$md5Key}", "{$s4Id}");
    }

    public function GetUrlUniqueListByKey($md5Key)
    {
        return $this->redis->hget(self::DWZ_URL_UNIQUE_SET_LIST_KEY, "{$md5Key}");
    }

    public function SetUrlListByS4id($s4Id, $uri)
    {
        return $this->redis->hset(self::DWZ_URL_LIST_KEY, "{$s4Id}", "{$uri}");
    }

    public function GetUrlListByS4id($s4Id)
    {
        return $this->redis->hget(self::DWZ_URL_LIST_KEY, "{$s4Id}");
    }

    public function GetZUrlFormatByS4Id($s4Id)
    {
        return env("DWZ_HOST", "http://127.0.0.1:9501") . "/z/{$s4Id}";
    }

    public function StatAccessNumIncr()
    {
        $randNum = max(rand(0, 30),1);
        $this->redis->incr(self::DWZ_ACCESS_NUM_DAY_STAT_KEY . date("Y-m-d"), $randNum);
        return $this->redis->incr(self::DWZ_ACCESS_NUM_STAT_KEY, $randNum);
    }

    public function GetStatAccessNum($day = null)
    {
        if (!empty($day)) {
            return $this->redis->get(self::DWZ_ACCESS_NUM_DAY_STAT_KEY . date("Y-m-d"));
        }
        return $this->redis->get(self::DWZ_ACCESS_NUM_STAT_KEY);
    }

    //统计ip
    public function SataSetIp($ip)
    {
        $this->redis->setbit(self::DWZ_ACCESS_NUM_IP_DAY_STAT_KEY . date("Y-m-d"), $ip, true);
        return !$this->redis->setbit(self::DWZ_ACCESS_NUM_IP_STAT_KEY, $ip, true);
    }

    public function GetStatIp($day = null)
    {
        if (!empty($day)) {
            return $this->redis->bitcount(self::DWZ_ACCESS_NUM_IP_DAY_STAT_KEY . date("Y-m-d"));
        }
        return $this->redis->bitcount(self::DWZ_ACCESS_NUM_IP_STAT_KEY) + 6000;
    }
}