<?php


namespace Magenest\QuickBooksDesktop\Api\Data;


use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Table structure
 *
 * Interface ShippingMethodInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface ShippingMethodInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__shipping_method';

    const ENTITY_ID = 'id';

    const SHIPPING_ID = 'shipping_id';

    const NOTE = 'note';
}