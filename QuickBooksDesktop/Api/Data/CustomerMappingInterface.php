<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/04/2020 08:36
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Table structure
 *
 * Interface CustomerMappingInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface CustomerMappingInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__customer_mapping';

    const ENTITY_ID = 'id';

    const M2_ENTITY_ID = 'm2_customer_id';

    const M2_ENTITY_TYPE = 'type';

    const QB_ID = 'qb_customer_id';

    const M2_ENTITY_TYPE_CUSTOMER = 3; // like \Magenest\QuickBooksDesktop\Api\Data\QueueInterface::TYPE_CUSTOMER

    const M2_ENTITY_TYPE_GUEST = 4; // like \Magenest\QuickBooksDesktop\Api\Data\QueueInterface::TYPE_GUEST
}