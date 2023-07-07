<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 15/12/2020 15:11
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;

/**
 * Class AbstractQuery
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
abstract class AbstractQuerySendRequestXML extends SendRequestXML
{

    /**
     * @inheritDoc
     */
    protected function getBodyXml()
    {
        // first transaction have processed = null
        $sessionConnect = $this->_sessionModel->create()->loadByCode($this->_currentSessionConnect->getData(SessionConnectInterface::SESSION_TOKEN));
        if (empty($sessionConnect->getData(SessionConnectInterface::PROCESSED)) && ($sessionConnect->getData(SessionConnectInterface::ITERATOR_ID) == SessionConnectInterface::ITERATOR_ID_START)) {
            return $this->getFirstXml();
        }

        return $this->getQueryXml();
    }

    /**
     * For the first request of the query transaction, if it is divided into more than 1 transaction, it must calculate the total number of requests to send.
     * This function to get total of records in Quickbooks
     *
     */
    abstract protected function getFirstXml();

    /**
     * This function query records to save into Magento
     *
     * @return string
     */
    abstract protected function getQueryXml();
}