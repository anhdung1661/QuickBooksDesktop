<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 18/02/2020 14:39
 */

namespace Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export;

use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;
use Magenest\QuickBooksDesktop\Helper\Configuration;

/**
 * Button create qwc file to query account from Quickbooks
 * @package Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export
 */
class QWCQueryAccount extends QWCButton
{
    /**
     * @inheritDoc
     * @return int
     */
    protected function getTypeQuery()
    {
        return TypeQuery::QUERY_ACCOUNT;
    }

    /**
     * @inheritDoc
     * @return array
     */
    protected function getButtonData()
    {
        return [
            'id' => 'create_qwc_accounts',
            'label' => __('Query Accounts'),
        ];
    }
}