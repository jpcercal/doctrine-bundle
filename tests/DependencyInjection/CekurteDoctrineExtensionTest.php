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

use Cekurte\DoctrineBundle\DependencyInjection\CekurteDoctrineExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CekurteDoctrinetExtensionTest
 *
 * @author João Paulo Cercal <jpcercal@gmail.com>
 *
 * @version 1.0
 */
class CekurteDoctrineExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new ContainerBuilder();

        $loader = new CekurteDoctrineExtension();

        $loader->load(array(), $this->configuration);
    }

    public function testParametersIsEmpty()
    {
        $this->assertEmpty($this->configuration->getParameterBag()->all());
    }

    public function testServicesIsEmpty()
    {
        $this->assertEmpty($this->configuration->getDefinitions());
    }
}
