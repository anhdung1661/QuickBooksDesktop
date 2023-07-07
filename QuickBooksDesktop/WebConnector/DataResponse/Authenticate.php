<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

use Magenest\QuickBooksDesktop\Helper\GenerateSessionToken;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magenest\QuickBooksDesktop\Model\UserFactory as UserModelFactory;
use Magenest\QuickBooksDesktop\WebConnector\DataRequest\UserConnect;

/**
 * return value after QWC send authenticate()
 * The constants are values defined by QWC
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
class Authenticate
{
    const INVALID_USER = 'nvu'; // return QWC if user is invalid
    const INVALID_USER_NONE = 'none'; // return QWC if not found the user
    const INVALID_USER_BUSY = 'busy'; // return QWC if user is busy
    const VALID_USER = ''; // return QWC if user is valid
    const DEFAULT_SESSION_TOKEN = '88888-88888-88888-88888';

    /**
     * return to QWC one of constants
     * @var array
     */
    protected $authenticateResult;

    /**
     * @var GenerateSessionToken
     */
    protected $_sessionToken;

    /**
     * @var UserModelFactory
     */
    protected $_userModelFactory;

    protected $_sessionConnectFactory;

    /**
     * Authenticate constructor.
     * @param SessionConnectFactory $sessionConnectFactory
     * @param UserModelFactory $userFactory
     * @param GenerateSessionToken $sessionToken
     */
    public function __construct(
        SessionConnectFactory $sessionConnectFactory,
        UserModelFactory $userFactory,
        GenerateSessionToken $sessionToken
    ) {
        $this->_sessionConnectFactory = $sessionConnectFactory;
        $this->_sessionToken = $sessionToken;
        $this->_userModelFactory = $userFactory;
    }

    /**
     * @param UserConnect $userConnect
     * @param bool $noDataExchange
     * @throws \Exception
     */
    public function processAuth($userConnect, $noDataExchange = false)
    {
        try {
            $authStatus = self::INVALID_USER;
            $sessionToken = self::DEFAULT_SESSION_TOKEN;

            $userModel = $this->_userModelFactory->create();

            if ($userModel->isActiveUser($userConnect->strUserName, $userConnect->strPassword)) {
                $authStatus = self::VALID_USER;
                $sessionToken = $this->_sessionToken->generate($userConnect->strUserName);
                $sessionConnectModel = $this->_sessionConnectFactory->create();
                $sessionConnectModel->saveSessionConnect($userConnect->strUserName, $sessionToken);
            }
            if ($noDataExchange) {
                $authStatus = self::INVALID_USER_NONE;
            }
            $this->authenticateResult = [$sessionToken, $authStatus];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
