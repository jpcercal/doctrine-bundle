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
     * @param  bool  $has
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSession(array $data = array(), $has = true)
    {
        $session = $this
            ->getMockBuilder('\\Symfony\\Component\\HttpFoundation\\Session\\Session')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $session
            ->expects($this->any())
            ->method('has')
            ->willReturn($has)
        ;

        $session
            ->expects($this->any())
            ->method('get')
            ->willReturn($data)
        ;

        return $session;
    }

    /**
     * @param  string $host
     * @param  string $database
     * @param  string $user
     * @param  string $password
     * @return array
     */
    private function getSessionData($host = 'fakehost', $database = 'fakedatabase', $user = 'fakeuser', $password = 'fakepass')
    {
        return array(
            ConnectionWrapper::PARAM_HOST     => $host,
            ConnectionWrapper::PARAM_DATABASE => $database,
            ConnectionWrapper::PARAM_USER     => $user,
            ConnectionWrapper::PARAM_PASSWORD => $password,
        );
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConnect()
    {
        $this->wrapper->setSession($this->getMockSession(array(), false));

        $this->wrapper->connect();
    }

    public function testConnectIsConnected()
    {
        $this->wrapper->setSession($this->getMockSession());

        $class = new \ReflectionClass($this->wrapper);

        $reflectionProperty = $class->getProperty('_isConnected');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->wrapper, true);

        $this->assertFalse($this->wrapper->connect());
    }

    public function testConnectSuccessfully()
    {
        $this->wrapper->setSession($this->getMockSession());

        $this->assertTrue($this->wrapper->connect());
    }

    public function testClose()
    {
        $class = new \ReflectionClass($this->wrapper);

        $reflectionProperty = $class->getProperty('_isConnected');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->wrapper, true);

        $this->assertTrue($this->wrapper->isConnected());

        $this->wrapper->close();

        $this->assertFalse($this->wrapper->isConnected());
    }

    public function testHasNewConnectionParams()
    {
        $class  = new \ReflectionClass($this->wrapper);

        $method = $class->getMethod('hasNewConnectionParams');

        $method->setAccessible(true);

        $sessionData = array(
            $this->getSessionData(),
            $this->getSessionData('fakehost', 'fake2database'),
        );

        $this->wrapper->setSession($this->getMockSession($sessionData[0]));

        $this->assertFalse($method->invokeArgs($this->wrapper, array($sessionData[0])));

        $this->assertTrue($method->invokeArgs($this->wrapper, array($sessionData[1])));

        $this->wrapper->setSession($this->getMockSession($sessionData[1]));

        $this->assertFalse($method->invokeArgs($this->wrapper, array($sessionData[1])));
    }

    public function testForceSwitch()
    {
        $this->wrapper->setSession($this->getMockSession($this->getSessionData()));

        $this->assertFalse($this->wrapper->forceSwitch('fakehost', 'fakedatabase', 'fakeuser', 'fakepass'));
    }

    public function testForceSwitchWithSuccessfully()
    {
        $this->wrapper->setSession($this->getMockSession($this->getSessionData()));

        $this->assertTrue($this->wrapper->forceSwitch('fake2host', 'fakedatabase', 'fakeuser', 'fakepass'));
    }

    public function testGetParams()
    {
        $this->wrapper->setSession($this->getMockSession());

        $this->assertEmpty($this->wrapper->getParams());

        $sessionData = array(
            $this->getSessionData(),
            $this->getSessionData('fakehost', 'fake2database'),
        );

        $this->wrapper->setSession($this->getMockSession($sessionData[0]));

        $this->assertEquals($sessionData[0], $this->wrapper->getParams());

        $this->wrapper->setSession($this->getMockSession($sessionData[1]));

        $this->assertEquals($sessionData[1], $this->wrapper->getParams());
    }

    public function testGetParamsKey()
    {
        $class  = new \ReflectionClass($this->wrapper);

        $method = $class->getMethod('getParamsKey');

        $method->setAccessible(true);

        $keys = $method->invokeArgs($this->wrapper, array());

        $this->assertEquals(5, count($keys));

        $this->assertTrue(in_array(ConnectionWrapper::PARAM_DRIVER_OPTIONS, $keys));

        $this->assertTrue(in_array(ConnectionWrapper::PARAM_HOST, $keys));

        $this->assertTrue(in_array(ConnectionWrapper::PARAM_DATABASE, $keys));

        $this->assertTrue(in_array(ConnectionWrapper::PARAM_USER, $keys));

        $this->assertTrue(in_array(ConnectionWrapper::PARAM_PASSWORD, $keys));
    }
}
