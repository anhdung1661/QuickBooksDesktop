<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

use Magento\Framework\App\ProductMetadata;

/**
 * a message string describing the server version and any other information that you want your user to see
 * @package Magenest\QuickBooksDesktop\Model\Result
 */
class ServerVersion
{
    /**
     * @var string
     */
    protected $serverVersionResult;

    /**
     * ServerVersion constructor.
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     */
    public function __construct(ProductMetadata $productMetadata)
    {
        $this->serverVersionResult = 'Magento ' . $productMetadata->getEdition() . ' ver.' . $productMetadata->getVersion();
    }
}
