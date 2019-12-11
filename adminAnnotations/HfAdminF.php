<?php
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

