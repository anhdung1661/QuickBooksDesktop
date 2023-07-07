<?php


namespace Magenest\QuickBooksDesktop\Api\Data;


use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Table structure
 *
 * Interface Mapping
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface TaxesMappingInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__taxes_mapping';

    /**#@+
     * Constants are the column names of the main table
     */
    const ENTITY_ID = 'id';

    const QUICKBOOKS_ENTITY_ID = 'qb_tax_id';

    const MAGENTO_ID = 'magento_tax_id';
}