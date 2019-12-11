<?php
declare(strict_types = 1);

namespace App\Service;

use Hyperf\Di\Annotation\Inject;

class BladeExt extends \Hyperf\View\Engine\BladeEngine
{
    /**
     * @Inject
     * @var HfAdminService
     */
    protected $HfService;

    public function render($template, $data, $config): string
    {
        $blade = new \duncan3dc\Laravel\BladeInstance($config['view_path'], $config['cache_path']);
        $blade->directive("auth",function($key){
            list($res,$user) = $this->HfService->getAuthUser();
            if(!$res || !isset($user[$key])){
                return "<?php echo '--' ?>";
            }
            return "<?php echo {$user[$key]} ?>";
        });
        return $blade->render($template, $data);
    }


}