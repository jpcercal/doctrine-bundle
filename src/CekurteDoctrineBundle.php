<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\DoctrineBundle;

use Cekurte\DoctrineBundle\DependencyInjection\Compiler\CompilerDoctrineConnectionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * CekurteDoctrineBundle
 *
 * @author João Paulo Cercal <jpcercal@gmail.com>
 */
class CekurteDoctrineBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CompilerDoctrineConnectionPass());
    }
}
