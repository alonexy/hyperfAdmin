<?php
namespace App\AdminAnnotations;

use Hyperf\Di\Annotation\AbstractAnnotation;


/**
 * @Annotation
 * @Target({"CLASS"})
 */
class HfAdminC extends AbstractAnnotation
{
    /**
     * @var null|string
     */
    public $Cname = '';
    /**
     * @var int
     */
    public $Csort = 11;
    /**
     * @var string
     */
    public $Cstyle = 'fa-book';
}

