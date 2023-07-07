<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 07/04/2020 10:29
 */

namespace Magenest\QuickBooksDesktop\Api\Data;

/**
 * Table structure
 *
 * Interface QueueInterface
 * @package Magenest\QuickBooksDesktop\Api\Data
 */
interface QueueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TABLE_NAME = 'magenest_qbd__queue';

    /**#@+
     * Constants are the column names of the main table
     */
    const ENTITY_ID = 'queue_id';

    const COMPANY_ID = 'company_id';

    const MAGENTO_ENTITY_ID = 'entity_id'; // entity_id

    const MAGENTO_ENTITY_TYPE = 'type';

    const ENQUEUE_TIME = 'enqueue_datetime';

    const DEQUEUE_TIME = 'dequeue_datetime';

    const STATUS = 'status';

    const ACTION = 'action';

    const PRIORITY = 'priority';

    const MESSAGE = 'msg';

    /**#@+
     * Constants are the value of the type's column
     */
    const TYPE_PAYMENT_METHOD = 1;

    const TYPE_SHIPPING_METHOD = 2;

    const TYPE_CUSTOMER = 3;

    const TYPE_GUEST = 4;

    const TYPE_PRODUCT = 5;

    const TYPE_SALES_ORDER = 6;

    const TYPE_INVOICE = 7;

    const TYPE_RECEIVE_PAYMENT = 8;

    const TYPE_CREDIT_MEMO = 9;

    const TYPE_ITEM_SHIPPING = 10;

    const TYPE_ITEM_DISCOUNT = 11;

    const TYPE_EDIT_INVOICE = 12;

    /**#@+
     * Constants are the value of the status column
     */
    const STATUS_QUEUE = 1;

    const STATUS_SUCCESS = 2;

    const STATUS_FAIL = 3;

    const STATUS_PROCESSING = 4;

    const STATUS_BLOCKED = 5;

    /**#@+
     * Constants are the value of the action column
     */
    const ACTION_ADD = 2;

    const ACTION_MODIFY = 1;

    const ACTION_DELETE = 3;

    /**#@+
     * Constants are the value of the priority column
     */
    const PRIORITY_PAYMENT_METHOD = 1;

    const PRIORITY_SHIPPING_METHOD = 1;

    const PRIORITY_ITEM_OTHER_CHARGE = 1;

    const PRIORITY_ITEM_DISCOUNT = 1;

    const PRIORITY_CUSTOMER = 2;

    const PRIORITY_GUEST = 2;

    const PRIORITY_PRODUCT = 3;

    const PRIORITY_SALES_ORDER = 4;

    const PRIORITY_INVOICE = 5;

    const PRIORITY_RECEIVE_PAYMENT = 6;

    const PRIORITY_CREDIT_MEMO = 7;

    const PRIORITY_DELETE_PAYMENT = 12;

    const PRIORITY_MODIFY_INVOICE = 13;
}