<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 15/04/2020 15:05
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Table structure
 *
 * Interface ItemMappingInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */

interface ItemMappingInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__item_mapping';

    const ENTITY_ID = 'id';

    const M2_PRODUCT_ID = 'm2_product_id';

    const QB_ITEM_ID = 'qb_item_id';
}