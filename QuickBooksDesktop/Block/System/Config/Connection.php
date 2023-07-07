<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Block\System\Config;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Model\CompanyFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class Connection
 * @package Magenest\QuickBooksDesktop\Block\System\Config
 */
class Connection extends \Magento\Config\Block\System\Config\Form\Field implements RendererInterface
{
    /**
     * @var \Magenest\QuickBooksDesktop\Model\Company
     */
    protected $_company;

    /**
     * Connection constructor.
     * @param Context $context
     * @param CompanyFactory $company
     * @param array $data
     */
    public function __construct(
        Context $context,
        CompanyFactory $company,
        array $data = []
    )
    {
        $this->_company = $company;
        parent::__construct($context, $data);
    }


    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $model = $this->_company->create()->load(CompanyInterface::COMPANY_CONNECTED, CompanyInterface::COMPANY_STATUS_FIELD);
        $name = $model->getCompanyName();
        if ($name) {
            return "<h2 style='color:green; text-align: center; font-weight: bold'>" . __('Company ') . $name . __(' is now connected') . "</h2>";
        } else {
            return "<h2 style='color:red; text-align: center; font-weight: bold'>" . __('No company is connected right now!') . "</h2>";
        }
    }
}
