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
    public function getParams()
    {
        if ($this->getSession()->has(self::SESSION_CONNECTION_KEY)) {
            $this->_params = array_merge($this->_params, $this->getSession()->get(self::SESSION_CONNECTION_KEY));
        }

        return $this->_params;
    }

    /**
     * @inheritdoc
     */
    public function isConnected()
    {
        return $this->_isConnected;
    }
}
