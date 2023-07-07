<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 11:03
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\ShippingMethodInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ShippingMethod
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class ShippingMethod extends AbstractQuickbooksEntity
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ShippingMethodInterface::TABLE_NAME, ShippingMethodInterface::ENTITY_ID);
    }

    /**
     * Save multiple shipping methods
     * @param $shippingMethodData
     * @return ShippingMethod
     */
    public function saveShippingMethods($shippingMethodData)
    {
        $this->saveQuickbooksEntity(ShippingMethodInterface::TABLE_NAME, $shippingMethodData);

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
            ->insertOnDuplicate($this->getMainTable(), $data, [ShippingMethodInterface::LIST_ID, ShippingMethodInterface::EDIT_SEQUENCE, ShippingMethodInterface::NOTE]);
    }
}