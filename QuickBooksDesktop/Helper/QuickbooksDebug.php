<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 * @author: doanhcn2
 * @time: 21/02/2020 14:45
 */

namespace Magenest\QuickBooksDesktop\Helper;

use Magenest\QuickBooksDesktop\Helper\Configuration;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class QuickbooksDebug
 * @package Magenest\QuickBooksDesktop\Helper
 */
class QuickbooksDebug extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/quickbooks.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * @var Configuration
     */
    protected $_moduleConfig;

    /**
     * QuickbooksDebug constructor.
     * @param Configuration $configuration
     * @param DriverInterface $filesystem
     * @param null $filePath
     * @param null $fileName
     * @throws \Exception
     */
    public function __construct(
        Configuration $configuration,
        DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
    ) {
        $this->_moduleConfig = $configuration;
        parent::__construct($filesystem, $filePath, $fileName);
    }

    /**
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record): bool
    {
        if ($this->_moduleConfig->isEnableDebugMode()) {
            return parent::isHandling($record);
        }
        return false;
    }
}
