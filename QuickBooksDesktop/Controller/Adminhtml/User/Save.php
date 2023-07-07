<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\User;

use Magenest\QuickBooksDesktop\Api\Data\UserInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\User as UserResourceModel;
use Magenest\QuickBooksDesktop\Model\ResourceModel\User\CollectionFactory as UserCollection;
use Magenest\QuickBooksDesktop\Model\UserFactory as UserModel;
use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\User
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var UserModel
     */
    protected $user;

    /**
     * @var UserCollection
     */
    protected $_userCollection;

    /**
     * @var UserResourceModel
     */
    protected $_userResourceModel;

    /**
     * Save constructor.
     * @param \Magento\Backend\Model\Session $backendSession
     * @param Action\Context $context
     * @param UserCollection $userCollection
     * @param UserResourceModel $userResource
     * @param UserModel $user
     */
    public function __construct(
        Action\Context $context,
        UserCollection $userCollection,
        UserResourceModel $userResource,
        UserModel $user
    ) {
        parent::__construct($context);
        $this->user = $user;
        $this->_userCollection = $userCollection;
        $this->_userResourceModel = $userResource;
    }

    /**
     * Save user
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            try {
                $model = $this->user->create();

                if (!empty($data['user_id'])) {
                    $userCollection = $this->_userCollection->create()->getItemById($data['user_id']);
                    if ($data['user_id'] != $userCollection->getUserId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Cannot find the user. Please try again!'));
                    }
                    $info = [
                        UserInterface::ENTITY_ID => $data['user_id'],
                        UserInterface::USERNAME_FIELD => $data['username'],
                        UserInterface::STATUS_FIELD => $data['status'],
                    ];

                    if (!empty($data['password'])) {
                        $info['password'] = hash('md5', $data['password']);
                    }
                    $checkUserName = $this->_userCollection->create()
                        ->addFieldToFilter(UserInterface::USERNAME_FIELD, $data['username'])
                        ->addFieldToFilter(UserInterface::ENTITY_ID, ['neq' => $data['user_id']])
                        ->getLastItem()->getData();
                    if (count($checkUserName)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__("Duplicate username, you cannot this user."));
                    }
                } else {
                    $info = [
                        UserInterface::USERNAME_FIELD => $data['username'],
                        UserInterface::PASSWORD_FIELD => hash('md5', $data['password']),
                        UserInterface::STATUS_FIELD => $data['status'],
                    ];

                    $checkUserName = $this->_userCollection->create()
                        ->addFieldToFilter('username', $data['username'])
                        ->getLastItem()->getData();
                    if (count($checkUserName)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__("Duplicate username, you cannot this user."));
                    }
                }

                $model->addData($info);
                $this->_getSession()->setFormData($model->getData());
                $this->_userResourceModel->save($model);
                $this->messageManager->addSuccessMessage(__('User has been saved.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
            } catch (\Exception $e) {
                $this->_getSession()->setFormData($data);
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
