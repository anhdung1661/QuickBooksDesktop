<?php


namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface Item
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface ItemInterface extends QuickbooksEntityInterface
{
    const TABLE_NAME = 'magenest_qbd__item';

    const ENTITY_ID = 'id';

    const ITEM_NAME = 'item_name';

    const ITEM_TYPE = 'item_type';

    const NOTE = 'note';

    const ITEM_TYPE_ITEM_INVENTORY = 1;

    const ITEM_TYPE_ITEM_NONE_INVENTORY = 2;
}