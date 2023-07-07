<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 15:56
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\PaymentMethodInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PaymentMethod
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class PaymentMethod extends AbstractQuickbooksEntity
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(PaymentMethodInterface::TABLE_NAME, PaymentMethodInterface::ENTITY_ID);
    }

    /**
     * @param $paymentMethodsData
     * @return PaymentMethod
     */
    public function savePaymentMethods($paymentMethodsData)
    {
        $this->saveQuickbooksEntity(PaymentMethodInterface::TABLE_NAME, $paymentMethodsData);

        return $this;
    }

    /**
     * @param $data
     * @return int
     * @throws LocalizedException
     */
    public function updateQuickbooksInformation($data)
    {
        return $this->getConnection()
            ->insertOnDuplicate($this->getMainTable(), $data, [PaymentMethodInterface::LIST_ID, PaymentMethodInterface::EDIT_SEQUENCE, PaymentMethodInterface::NOTE]);
    }
}