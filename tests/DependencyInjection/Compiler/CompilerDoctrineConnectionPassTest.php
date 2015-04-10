<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\DoctrineBundle\Tests\DependencyInjection;

use Cekurte\DoctrineBundle\DependencyInjection\Compiler\CompilerDoctrineConnectionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CompilerDoctrineConnectionPassTest
 *
 * @author João Paulo Cercal <jpcercal@gmail.com>
 *
 * @version 1.0
 */
class CompilerDoctrineConnectionPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new CompilerDoctrineConnectionPass());

        $definition = new Definition('Symfony\Component\DependencyInjection\Definition');
        $definition->addTag('fake');

        $container->setDefinition('doctrine.dbal.dynamic_connection', $definition);

        $container->register('session', '\stdClass');
        $container->compile();

        $doctrine = $container->getDefinition('doctrine.dbal.dynamic_connection');

        $calls = $doctrine->getMethodCalls();

        $this->assertCount(1, $calls);
        $this->assertEquals('setSession', $calls[0][0]);
        $this->assertEquals(new Reference('session'), $calls[0][1][0]);
    }
}
