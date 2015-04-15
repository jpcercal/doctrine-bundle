<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\DoctrineBundle\Tests;

use Cekurte\DoctrineBundle\CekurteDoctrineBundle;
use Cekurte\DoctrineBundle\DependencyInjection\Compiler\CompilerDoctrineConnectionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CekurteDoctrineBundleTest
 *
 * @author João Paulo Cercal <jpcercal@gmail.com>
 *
 * @version 1.0
 */
class CekurteDoctrineBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testInheritedOfBundle()
    {
        $reflection = new \ReflectionClass('\\Cekurte\\DoctrineBundle\\CekurteDoctrineBundle');

        $this->assertTrue($reflection->isSubclassOf(
            '\\Symfony\\Component\\HttpKernel\\Bundle\\Bundle'
        ));
    }

    public function testBuild()
    {
        $containerBuilder = new ContainerBuilder();

        $bundle = new CekurteDoctrineBundle();

        $bundle->build($containerBuilder);

        $passes = $containerBuilder->getCompilerPassConfig()->getPasses();

        $found = false;

        foreach ($passes as $pass) {
            if ($pass instanceof CompilerDoctrineConnectionPass) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
    }
}
