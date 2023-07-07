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

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection;
use Magento\Backend\Block\Template\Context;

abstract class QWCButton extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Configuration
     */
    protected $_moduleConfig;

    /**
     * @var Collection
     */
    protected $_companyCollection;

    /**
     * QWCButton constructor.
     * @param Context $context
     * @param Configuration $moduleConfig
     * @param Collection $companyCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Configuration $moduleConfig,
        Collection $companyCollection,
        array $data = []
    )
    {
        $this->_companyCollection = $companyCollection;
        $this->_moduleConfig = $moduleConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock */
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = [
            'website' => $buttonBlock->getRequest()->getParam('website'),
            'type' => $this->getTypeQuery()
        ];

        $url = $this->getUrl("qbdesktop/QWC/export", $params);

        $data = $this->getButtonData();
        $data['onclick'] = "setLocation('" . $url . "')";

        if ($this->isDisabled()) {
            $data['disabled'] = 'disabled';
        }

        return $buttonBlock->setData($data)->toHtml();
    }

    /**
     * @return TypeQuery
     */
    abstract protected function getTypeQuery();

    /**
     * @return array
     */
    abstract protected function getButtonData();

    /**
     * @return bool
     */
    protected function isDisabled()
    {
        $config = $this->_moduleConfig->getUserConnect();

        $isConnectedCompany = $this->_companyCollection->addFieldToFilter(CompanyInterface::COMPANY_STATUS_FIELD, CompanyInterface::COMPANY_CONNECTED)->count();

        if (!$config || !$isConnectedCompany) {
            return true;
        }

        return false;
    }
}