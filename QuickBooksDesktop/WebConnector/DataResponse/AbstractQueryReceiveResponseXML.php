<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 15/12/2020 17:18
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

/**
 * Class AbstractQueryReceiveResponseXML
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
abstract class AbstractQueryReceiveResponseXML extends ReceiveResponseXML
{

    /**
     * @return int
     */
    protected function getTotalProcess()
    {
        $maxReturn = $this->getMaxReturned() ?? 1;
        $totalItems = $this->setResponseData(
            $this->getResponseByRequestId($this->getListRequestId()[0])
        )->countObjectsReturn();
        return ceil( $totalItems / $maxReturn) + 1;
    }

    /**
     * @return int
     */
    abstract protected function getMaxReturned();
}