<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Model\ResourceModel;

use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class Tax
 * @package Magenest\QuickBooksDesktop\Model\ResourceModel
 */
class Taxes extends AbstractQuickbooksEntity
{
    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init(TaxesInterface::TABLE_NAME, TaxesInterface::ENTITY_ID);
    }

    /**
     * @param $taxes
     * @return Taxes
     */
    public function saveTaxes($taxes)
    {
        $this->saveQuickbooksEntity(TaxesInterface::TABLE_NAME, $taxes);

        return $this;
    }
}
