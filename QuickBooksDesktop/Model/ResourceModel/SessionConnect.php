<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\SessionConnectInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SessionConnect
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class SessionConnect extends AbstractDb
{
    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init(SessionConnectInterface::TABLE_NAME, SessionConnectInterface::ENTITY_ID);
    }

    /**
     * @param $userName
     * @param $sessionToken
     * @param $processed
     * @param $total
     * @param $iteratorId
     * @return SessionConnect
     * @throws \Exception
     */
    public function saveSessionConnect($userName, $sessionToken, $processed, $total, $iteratorId)
    {
        try {
            $connection = $this->getConnection();

            $connection->insertOnDuplicate(
                $this->getTable(SessionConnectInterface::TABLE_NAME),
                [
                    SessionConnectInterface::USER_NAME_ID => $userName,
                    SessionConnectInterface::SESSION_TOKEN => $sessionToken,
                    SessionConnectInterface::PROCESSED => $processed,
                    SessionConnectInterface::TOTAL => $total,
                    SessionConnectInterface::ITERATOR_ID => $iteratorId
                ], [
                    SessionConnectInterface::PROCESSED => $processed,
                    SessionConnectInterface::TOTAL => $total
                ]
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * @param $sessionToken
     * @param $msg
     * @return $this
     * @throws \Exception
     */
    public function setLastErrorMsg($sessionToken, $msg)
    {
        try {
            $connection = $this->getConnection();

            $connection->update(
                $this->getTable(SessionConnectInterface::TABLE_NAME),
                [
                    SessionConnectInterface::LAST_ERROR_MESSAGE => $msg
                ],
                    "`" . SessionConnectInterface::SESSION_TOKEN . "` = '{$sessionToken}'"
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * @param $sessionToken
     * @param $total
     * @return $this
     * @throws \Exception
     */
    public function setTotal($sessionToken, $total)
    {
        try {
            $connection = $this->getConnection();

            $connection->update(
                $this->getTable(SessionConnectInterface::TABLE_NAME),
                [
                    SessionConnectInterface::TOTAL => $total
                ],
                "`" . SessionConnectInterface::SESSION_TOKEN . "` = '{$sessionToken}'"
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * @param $sessionToken
     * @param $processed
     * @return $this
     * @throws \Exception
     */
    public function setProcessed($sessionToken, $processed)
    {
        try {
            $connection = $this->getConnection();

            $connection->update(
                $this->getTable(SessionConnectInterface::TABLE_NAME),
                [
                    SessionConnectInterface::PROCESSED => $processed
                ],
                "`" . SessionConnectInterface::SESSION_TOKEN . "` = '{$sessionToken}'"
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * @param $sessionToken
     * @param $iteratorId
     * @return $this
     * @throws \Exception
     */
    public function setIterator($sessionToken, $iteratorId)
    {
        try {
            $connection = $this->getConnection();

            $connection->update(
                $this->getTable(SessionConnectInterface::TABLE_NAME),
                [
                    SessionConnectInterface::ITERATOR_ID => $iteratorId
                ],
                "`" . SessionConnectInterface::SESSION_TOKEN . "` = '{$sessionToken}'"
            );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
        return $this;
    }
}
