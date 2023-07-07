<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\User;

use Magenest\QuickBooksDesktop\Controller\Adminhtml\User as AbstractUser;
use Magenest\QuickBooksDesktop\Model\ResourceModel\User\CollectionFactory as UserFactory;
use Magenest\QuickBooksDesktop\Model\UserFactory as UserModel;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Edit
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\User
 */
class Edit extends AbstractUser
{
    /**
     * @var UserModel
     */
    protected $_userModel;

    /**
     * Edit constructor.
     * @param UserModel $userModel
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param UserFactory $userFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Filter $filter
     */
    public function __construct(
        UserModel $userModel,
        Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, UserFactory $userFactory,
        ForwardFactory $resultForwardFactory, Filter $filter
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $userFactory, $resultForwardFactory, $filter);
        $this->_userModel = $userModel;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_QuickBooksDesktop::user')
            ->addBreadcrumb(__('Manage User'), __('Manage User'));

        return $resultPage;
    }

    /**
     * @return Edit|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_userModel->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This user no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_getSession()->getFormData();
        if (!empty($data)) {
            $model->addData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('user', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend(__('Information'));

        return $resultPage;
    }
}
