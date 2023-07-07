<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Block\System\Config\Form\Field;

use Magenest\QuickBooksDesktop\Block\System\Config\Form\Field\Export\QWCButton;
use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Disable
 * @param AbstractElement $element
 * @package Magenest\QuickBooksDesktop\Block\System\Config\Form\Field
 */
class Disable extends QWCButton
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->isDisabled()) {
            $element->setData('disabled', true);
        }
        return $element->getElementHtml();
    }

    /**
     * @inheritDoc
     */
    protected function getTypeQuery()
    {
        // TODO: Implement getTypeQuery() method.
    }

    /**
     * @inheritDoc
     */
    protected function getButtonData()
    {
        // TODO: Implement getButtonData() method.
    }
}
