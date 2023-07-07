<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magento\Framework\Model\AbstractModel;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

/**
 * Class SessionConnect
 * @package Magenest\QuickBooksDesktop\Model
 * @method int getId()
 * @method string getSessionToken()
 * @method string getUserName()
 * @method string geCreatedAt()
 * @method string getProcessed()
 * @method string getCurrent()
 * @method string getIpAddr()
 * @method string getLastErrorMsg()
 */
class SessionConnect extends AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = SessionConnectInterface::TABLE_NAME;

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\SessionConnect');
    }

    /**
     * @param $sessionToken
     * @return SessionConnect
     */
    public function loadByCode($sessionToken)
    {
        return $this->load($sessionToken, SessionConnectInterface::SESSION_TOKEN);
    }

    /**
     * @param $userName
     * @param $sessionToken
     * @param int $processed
     * @param int $total
     * @param int $iteratorId
     * @return SessionConnect
     */
    public function saveSessionConnect($userName, $sessionToken, $processed = SessionConnectInterface::PROCESSED_DEFAULT, $total = 1, $iteratorId = SessionConnectInterface::ITERATOR_ID_NONE)
    {
        $this->getResource()->saveSessionConnect($userName, $sessionToken, $processed, $total, $iteratorId);
        return $this;
    }

    /**
     * @param $sessionToken
     * @param $msg
     * @return SessionConnect
     * @throws \Exception
     */
    public function setLastErrorMsg($sessionToken, $msg)
    {
        $this->getResource()->setLastErrorMsg($sessionToken, $msg);
        return $this;
    }

    /**
     * set the number of requests per transaction
     *
     * @param $sessionToken
     * @param $total
     * @return SessionConnect
     */
    public function setTotal($sessionToken, $total)
    {
        $this->getResource()->setTotal($sessionToken, $total);
        return $this;
    }

    /**
     * update the number of requests that be processed
     *
     * @param $sessionToken
     * @param $number
     * @return SessionConnect
     */
    public function updateProcessed($sessionToken, $number)
    {
        $processed = $number + $this->loadByCode($sessionToken)->getData(SessionConnectInterface::PROCESSED);
        $this->getResource()->setProcessed($sessionToken, $processed);
        return $this;
    }

    /**
     * update the number of requests that be processed
     *
     * @param $sessionToken
     * @param $iteratorId
     * @return SessionConnect
     */
    public function updateIteratorId($sessionToken, $iteratorId)
    {
        if (!empty($iteratorId)) {
            $this->getResource()->setIterator($sessionToken, $iteratorId);
        }
        return $this;
    }
}
