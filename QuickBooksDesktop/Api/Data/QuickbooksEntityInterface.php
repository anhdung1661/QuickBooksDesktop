<?php


namespace Magenest\QuickBooksDesktop\Api\Data;


/**
 * Table structure
 *
 * Interface QuickbooksEntityInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface QuickbooksEntityInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const COMPANY_ID = 'company_id';

    const LIST_ID = 'list_id';

    const EDIT_SEQUENCE = 'edit_sequence';
}