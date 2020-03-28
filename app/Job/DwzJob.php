<?php

declare(strict_types = 1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;
use App\Model\DwzLog;
use App\Model\DwzAccessLog;
use Hyperf\DbConnection\Db;
use App\Service\DwzService;
use Hyperf\Di\Annotation\Inject;

class DwzJob extends Job
{
    public $params;

    public function __construct($params)
    {
        $this->params = $params;

    }

    public function handle()
    {
        try {
            $data = json_decode($this->params, true);
            //stat
            $DwzService = new DwzService();
            $DwzService->StatAccessNumIncr();

            if ($data['type'] == 1) {
                unset($data['type']);
                $data['uk'] = md5($data['uri']);
                $ip         = $data['ip'];
                $res        = Db::select("SELECT inet_aton('{$ip}') as ip limit 1;");
                $arr        = collect($res[0])->toArray();
                if (isset($arr['ip'])) {
                    $DwzService->SataSetIp($arr['ip']);
                    $data['ip'] = $arr['ip'];
                    /** @var DwzLog $dwzlog */
                    Dwzlog::create($data);
                }
            }
            else {
                unset($data['type']);
                $ip         = $data['ip'];
                $res        = Db::select("SELECT inet_aton('{$ip}') as ip limit 1;");
                $arr        = collect($res[0])->toArray();
                if (isset($arr['ip'])) {
                    $DwzService->SataSetIp($arr['ip']);
                    $data['ip'] = $arr['ip'];
                    /** @var DwzAccessLog $dwzAccesssLog */
                    DwzAccessLog::create($data);
                }
            }

        }
        catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
