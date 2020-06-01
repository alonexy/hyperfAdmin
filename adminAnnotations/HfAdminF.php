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

namespace App\AdminAnnotations;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class HfAdminF extends AbstractAnnotation
{
    /**
     * @var null|string
     */
    public $Fname = '';

    /**
     * @var bool
     */
    public $Fdisplay = true;

    /**
     * @var string
     */
    public $Fstyle = 'fa-circle-o';
}
