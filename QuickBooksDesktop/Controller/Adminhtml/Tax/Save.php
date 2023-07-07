<?php
/**
 * Created by PhpStorm.
 * User: namnt
 * Date: 6/30/18
 * Time: 3:23 PM
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Tax;

use Magenest\QuickBooksDesktop\Model\TaxesMappingFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Save
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Tax
 */
class Save extends \Magento\Backend\App\Action
{

    protected $_taxesMappingFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param TaxesMappingFactory $taxesMappingFactory
     */
    public function __construct(
        Action\Context $context,
        TaxesMappingFactory $taxesMappingFactory
    ) {
        $this->_taxesMappingFactory = $taxesMappingFactory;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $taxesMappingModel = $this->_taxesMappingFactory->create();
            $taxesMappingModel->addMultiRowsData($this->getRequest()->getPostValue('taxRow'))->save();
            $this->messageManager->addSuccessMessage("Save Success!");
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
