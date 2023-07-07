<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * QuickBooks Desktop extension
 * NOTICE OF LICENSE
 *
 * @author doanhcn2 - Magenest
 * @time: 19/10/2020 17:29
 */

namespace Magenest\QuickBooksDesktop\Controller\Adminhtml\Config;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

/**
 * Class DownloadDebug
 * @package Magenest\QuickBooksDesktop\Controller\Adminhtml\Config
 */
class DownloadDebug extends \Magento\Backend\App\Action
{
    /**
     * @var DirectoryList
     */
    protected $directory_list;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * DownloadDebug constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param DirectoryList $directory_list
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        DirectoryList $directory_list,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->directory_list = $directory_list;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function execute()
    {
        $version = $this->getRequest()->getParam('version');
        $filename = "qbd_debugfile_".$version."_".date("Ymd").".log";
        $file = $this->directory_list->getPath("var")."/log/quickbooks.log";
        return $this->fileFactory->create($filename, file_get_contents($file), "tmp");
    }
}
