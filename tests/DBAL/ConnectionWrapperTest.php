<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\DoctrineBundle\Tests\DBAL;

use Cekurte\DoctrineBundle\DBAL\ConnectionWrapper;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ConnectionWrapperTest
 *
 * @author João Paulo Cercal <jpcercal@gmail.com>
 *
 * @version 1.0
 */
class ConnectionWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConnectionWrapper
     */
    private $wrapper;

    public function setUp()
    {
        $this->wrapper = new ConnectionWrapper(
            array(),
            $this->getMock('\\Doctrine\\DBAL\\Driver')
        );
    }

    /**
     * @param  array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSession(array $data = array())
    {
        $session = $this
            ->getMockBuilder('\\Symfony\\Component\\HttpFoundation\\Session\\Session')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $session
            ->expects($this->any())
            ->method('has')
            ->willReturn(true)
        ;

        $session
            ->expects($this->any())
            ->method('get')
            ->willReturn($data)
        ;

        return $session;
    }

    public function testInheritedOfDoctrineConnection()
    {
        $reflection = new \ReflectionObject($this->wrapper);

        $this->assertTrue($reflection->isSubclassOf(
            '\\Doctrine\\DBAL\\Connection'
        ));
    }

    public function testSetSession()
    {
        $this->wrapper->setSession($this->getMockSession());

        $this->assertInstanceOf(
            '\\Symfony\\Component\\HttpFoundation\\Session\\Session',
            $this->wrapper->getSession()
        );
    }

    public function testGetSession()
    {
        $this->assertNull($this->wrapper->getSession());
    }

    public function testIsConnected()
    {
        $this->assertFalse($this->wrapper->isConnected());
    }

    public function testConnect()
    {
    }

    public function testClose()
    {
    }

    public function testForceSwitch()
    {
    }

    public function testGetParams()
    {
        $this->wrapper->setSession($this->getMockSession());

        $this->assertEmpty($this->wrapper->getParams());

        $sessionFirstData = array(
            ConnectionWrapper::PARAM_HOST     => 'fakehost',
            ConnectionWrapper::PARAM_DATABASE => 'fakedatabase',
            ConnectionWrapper::PARAM_USER     => 'fakeuser',
            ConnectionWrapper::PARAM_PASSWORD => 'fakepass',
        );

        $this->wrapper->setSession($this->getMockSession($sessionFirstData));

        $this->assertEquals($sessionFirstData, $this->wrapper->getParams());

        $sessionSecondData = array(
            ConnectionWrapper::PARAM_HOST     => 'fakehost',
            ConnectionWrapper::PARAM_DATABASE => 'fake2database',
            ConnectionWrapper::PARAM_USER     => 'fakeuser',
            ConnectionWrapper::PARAM_PASSWORD => 'fakepass',
        );

        $this->wrapper->setSession($this->getMockSession($sessionSecondData));

        $this->assertEquals($sessionSecondData, $this->wrapper->getParams());
    }
}
