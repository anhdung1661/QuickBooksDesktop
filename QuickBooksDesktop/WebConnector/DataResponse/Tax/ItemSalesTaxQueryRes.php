<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 03/03/2020 17:16
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax;

/**
 * Interface QueryTaxRes
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Tax
 */
interface ItemSalesTaxQueryRes
{
    /**#@+
     * Constants defined by QBWC
     */

    const DETAIL_NAME = 'ItemSalesTaxRet';

    const TAX_NAME = 'Name';

    const TAX_RATE = 'TaxRate';

    const TAX_DES = 'ItemDesc';

    const LIST_ID = 'ListID';

    const EDIT_SEQUENCE = 'EditSequence';
}