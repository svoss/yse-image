<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 11:44
 */

namespace ISTI\Image;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ISTI\Image\DependencyInjection\Compiler;
use ISTI\Image\DependencyInjection\Compiler\ImageCompilerPass;
class ISTIImageBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ImageCompilerPass());
    }
} 