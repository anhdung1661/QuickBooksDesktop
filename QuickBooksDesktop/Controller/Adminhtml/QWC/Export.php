<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\QWC;

use Magenest\QuickBooksDesktop\Helper\CreateQWCFile;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company;
use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magenest\QuickBooksDesktop\Helper\Configuration;

/**
 * Class Export
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\QWC
 */
class Export extends \Magento\Backend\App\Action
{
    /**
     * @var CreateQWCFile
     */
    protected $_qwcFile;

    /**
     * @var Company
     */
    protected $_companyModel;

    /**
     * @var Configuration
     */
    protected $configWriter;

    /**
     * Export constructor.
     * @param CreateQWCFile $_qwcFile
     * @param Company $companyModel
     * @param Context $context
     * @param Configuration $configWriter
     */
    public function __construct(
        CreateQWCFile $_qwcFile,
        Company $companyModel,
        Context $context,
        Configuration $configWriter
    ) {
        parent::__construct($context);
        $this->_companyModel = $companyModel;
        $this->_qwcFile = $_qwcFile;
        $this->configWriter = $configWriter;
    }

    /**
     * @return ResponseInterface|ResultInterface|null
     */
    public function execute()
    {
        try {
            if (TypeQuery::QUERY_DISCONNECT == $this->getRequest()->getParam('type')) {
                $this->_companyModel->disableAllCompany();
                $this->configWriter->setConfigData(Configuration::PATH_COMPANY, "");
                return $this->_redirect('adminhtml/system_config/edit/section/qbdesktop');
            }
            $qwcFile = $this->_qwcFile->createFileQWC($this->getRequest()->getParam('type'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while create file.'));
        }
        return $qwcFile;
    }


    /**
     * Always true
     *
     * @return bool
     */
    public function _isAllowed()
    {
        return true;
    }
}
