<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/02/2020 10:30
 */

namespace Magenest\QuickBooksDesktop\Helper;

use \Magento\Framework\App\Response\Http\FileFactory;
use Magenest\QuickBooksDesktop\Model\Config\Source\Queue\TypeQuery;
use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magenest\QuickBooksDesktop\Model\ResourceModel\User\CollectionFactory as UserCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

/**
 * Class CreateQWCFile
 * @package Magenest\QuickBooksDesktop\Helper
 */
class CreateQWCFile extends \Magento\Framework\App\Helper\AbstractHelper
{
    const NONCE = '0123456789ABCDEF';

    const QWC_SYNC_QUEUE = 'Synchronization from Magento';

    const QWC_CONNECT_COMPANY = 'Connect Company';

    const QWC_MAPPING_TAX = 'Mapping Tax';

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ReadFactory
     */
    protected $_readFactory;

    /**
     * @var Reader
     */
    protected $_reader;

    /**
     * @var UserCollection
     */
    protected $_userColection;

    /**
     * @var Configuration
     */
    protected $_moduleConfig;

    /**
     * CreateQWCFile constructor.
     * @param FileFactory $fileFactory
     * @param ReadFactory $readFactory
     * @param Reader $reader
     * @param Configuration $moduleConfig
     * @param UserCollection $userCollection
     * @param Context $context
     */
    public function __construct(
        FileFactory $fileFactory,
        ReadFactory $readFactory,
        Reader $reader,
        Configuration $moduleConfig,
        UserCollection $userCollection,
        Context $context
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_readFactory = $readFactory;
        $this->_reader = $reader;
        $this->_userColection = $userCollection;
        $this->_moduleConfig = $moduleConfig;
        parent::__construct($context);
    }

    /**
     * @param $fileType
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function createFileQWC($fileType)
    {
        try {
            if ($fileType == TypeQuery::QUERY_COMPANY) {
                $fileName = 'connect-company.qwc';
                $appName = self::QWC_CONNECT_COMPANY;
                $appUrl = $this->_getUrl( 'qbdesktop/connection_company/connect');
            } elseif ($fileType == TypeQuery::QUERY_TAX) {
                $fileName = 'query-tax.qwc';
                $appName = self::QWC_MAPPING_TAX;
                $appUrl = $this->_getUrl('qbdesktop/connection_tax/query');
            } else {
                $fileName = 'sync-queue.qwc';
                $appName = self::QWC_SYNC_QUEUE;
                $appUrl = $this->_getUrl('qbdesktop/connection_queue/syncData');
            }

            $userId = $this->_moduleConfig->getUserConnect();
            $userName = $this->_userColection->create()->getItemById($userId)->getUsername();
            $ownerId = $this->getNonce(8) . '-' . $this->getNonce(4) . '-' . $this->getNonce(4) . '-' . $this->getNonce(4) . '-' . $this->getNonce(12);
            $fileId = $this->getNonce(8) . '-' . $this->getNonce(4) . '-' . $this->getNonce(4) . '-' . $this->getNonce(4) . '-' . $this->getNonce(12);
            $timeAutoRun = $this->_moduleConfig->getTimeAutoRun();
            $fileData = [
                '{{AppName}}' => $appName,
                '{{AppURL}}' => $appUrl,
                '{{CertURL}}' => $this->_getUrl(''),
                '{{username}}' => $userName,
                '{{supportURL}}' => $this->_getUrl('') . 'support.php',
                '{{OwnerID}}' => '{' . $ownerId . '}',
                '{{FileID}}' => '{' . $fileId . '}',
                '{{minutes}}' => $timeAutoRun,
            ];

            return $this->_fileFactory->create(
                $fileName,
                $this->getQWCFileContent($fileData),
                DirectoryList::VAR_DIR
            );
        } catch (FileSystemException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Return file *.qwc content to add into Quickbooks web connector
     *
     * @param array fileData
     * @return string fileContent
     * @throws \Magento\Framework\Exception\FileSystemException
     * @api
     */
    public function getQWCFileContent($fileData)
    {
        $moduleEtcPath = $this->_reader->getModuleDir(Dir::MODULE_ETC_DIR, Configuration::MODULE_NAME);
        $configFilePath = $moduleEtcPath . '/qwc/sample.qwc';
        $directoryRead = $this->_readFactory->create($moduleEtcPath);
        $configFilePath = $directoryRead->getRelativePath($configFilePath);
        $originalFileData = $directoryRead->readFile($configFilePath);

        return strtr($originalFileData, $fileData);
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|string[]
     */
    public function _getUrl($route, $params = [])
    {
        $url = parent::_getUrl($route, $params);
        if (strpos($url, "://localhost") !== false) {
            if (strpos($url, 'https://') === 0) {
                $url = str_replace("https", "http", $url);
            }
        } else {
            if (strpos($url, 'https://') !== 0) {
                $url = str_replace("http", "https", $url);
            }
        }
        return $url;
    }

    /**
     * Random string with length
     *
     * @param int $length
     * @return string
     */
    protected function getNonce($length = 32)
    {
        $tmp = str_split(self::NONCE);
        shuffle($tmp);

        return substr(implode('', $tmp), 0, $length);
    }
}