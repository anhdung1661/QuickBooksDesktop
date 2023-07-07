<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export;

use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;

/**
 * Button create qwc file mapping tax
 *
 * @package Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export
 */
class QWCMappingTax extends QWCButton
{
    /**
     * @inheritDoc
     * @return int
     */
    protected function getTypeQuery()
    {
        return TypeQuery::QUERY_TAX;
    }

    /**
     * @inheritDoc
     * @return array
     */
    protected function getButtonData()
    {
        return [
            'id' => 'create_qwc_map',
            'label' => __('Query Taxes'),
        ];
    }
}
