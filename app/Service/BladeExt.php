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
        $blade->directive('auth', function ($key) {
            list($res, $user) = $this->HfService->getAuthUser();
            if (! $res || ! isset($user[$key])) {
                return "<?php echo '--' ?>";
            }
            return "<?php echo {$user[$key]} ?>";
        });
        return $blade->render($template, $data);
    }
}
