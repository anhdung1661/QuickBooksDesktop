<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 20/04/2020 14:09
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd;

use Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\AbstractQueueAddReceiveResponseXML;

/**
 * Class ReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ItemDiscount\ItemDiscountAdd
 */
class ReceiveResponseXML extends AbstractQueueAddReceiveResponseXML implements ItemDiscountAddRes
{

    /**
     * @inheritDoc
     */
    protected function saveEntityData($listQueue)
    {
        // TODO: Implement saveEntityData() method.
    }
}