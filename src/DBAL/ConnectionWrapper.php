<?php

/*
 * This file is part of the Cekurte package.
 *
 * (c) João Paulo Cercal <jpcercal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\DoctrineBundle\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ConnectionWrapper
 *
 * @author João Paulo Cercal  <jpcercal@gmail.com>
 * @author Dawid zulus Pakula <zulus@w3des.net>
 *
 * @see http://stackoverflow.com/questions/6409167/symfony-2-multiple-and-dynamic-database-connection
 *
 * @version 1.0
 */
class ConnectionWrapper extends Connection
{
    /**
     * @var string
     */
    const SESSION_CONNECTION_KEY = 'doctrine_active_dynamic_connection';

    /**
     * @var string
     */
    const PARAM_DRIVER_OPTIONS = 'driverOptions';

    /**
     * @var string
     */
    const PARAM_HOST = 'host';

    /**
     * @var string
     */
    const PARAM_DATABASE = 'dbname';

    /**
     * @var string
     */
    const PARAM_USER = 'user';

    /**
     * @var string
     */
    const PARAM_PASSWORD = 'password';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var bool
     */
    private $_isConnected = false;

    /**
     * @var array
     */
    private $_params = array();

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @inheritdoc
     */
    public function isConnected()
    {
        return $this->_isConnected;
    }

    /**
     * @inheritdoc
     */
    public function connect()
    {
        if (!$this->getSession()->has(self::SESSION_CONNECTION_KEY)) {
            throw new \InvalidArgumentException('You have to inject into valid context first');
        }

        if ($this->isConnected()) {
            return false;
        }

        $params = $this->getParams();

        $driverOptions = isset($params[self::PARAM_DRIVER_OPTIONS])
            ? $params[self::PARAM_DRIVER_OPTIONS]
            : array()
        ;

        $this->_conn = $this->_driver->connect(
            $params,
            isset($params[self::PARAM_USER]) ? $params[self::PARAM_USER] : null,
            isset($params[self::PARAM_PASSWORD]) ? $params[self::PARAM_PASSWORD] : null,
            $driverOptions
        );

        $this->_isConnected = true;

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $this->_eventManager->dispatchEvent(Events::postConnect, new ConnectionEventArgs($this));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if ($this->isConnected()) {
            parent::close();
            $this->_isConnected = false;
        }
    }

    /**
     * Force a switch of database connection.
     *
     * @param  string $dbHost
     * @param  string $dbName
     * @param  string $dbUser
     * @param  string $dbPassword
     * @param  array  $dbOptions
     *
     * @return bool
     */
    public function forceSwitch($dbHost, $dbName, $dbUser, $dbPassword, array $dbOptions = array())
    {
        if ($this->getSession()->has(self::SESSION_CONNECTION_KEY)) {
            $current = $this->getSession()->get(self::SESSION_CONNECTION_KEY);
            if ($current[self::PARAM_HOST] === $dbHost && $current[self::PARAM_DATABASE] === $dbName) {
                return false;
            }
        }

        $this->getSession()->set(self::SESSION_CONNECTION_KEY, array(
            self::PARAM_DRIVER_OPTIONS => $dbOptions,
            self::PARAM_HOST           => $dbHost,
            self::PARAM_DATABASE       => $dbName,
            self::PARAM_USER           => $dbUser,
            self::PARAM_PASSWORD       => $dbPassword,
        ));

        if ($this->isConnected()) {
            $this->close();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getParams()
    {
        if ($this->getSession()->has(self::SESSION_CONNECTION_KEY)) {
            $this->_params = array_merge($this->_params, $this->getSession()->get(self::SESSION_CONNECTION_KEY));
        }

        return $this->_params;
    }

    /**
     * Get a list of parameter keys
     *
     * @return array
     */
    protected function getParamsKey()
    {
        return array(
            self::PARAM_DRIVER_OPTIONS,
            self::PARAM_HOST,
            self::PARAM_DATABASE,
            self::PARAM_USER,
            self::PARAM_PASSWORD,
        );
    }

    /**
     * Checks if has new connection parameters
     *
     * @param  array $params
     *
     * @return bool
     */
    protected function hasNewConnectionParams(array $params)
    {
        if (!$this->getSession()->has(self::SESSION_CONNECTION_KEY)) {
            return true;
        }

        $currentParams = $this->getSession()->get(self::SESSION_CONNECTION_KEY);

        foreach ($this->getParamsKey() as $key) {
            if (isset($currentParams[$key]) && $params[$key] !== $currentParams[$key]) {
                return true;
            }
        }

        return false;
    }
}
