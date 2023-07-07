<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */
namespace Magenest\QuickBooksDesktop\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Company
 * @package Magenest\QuickBooksDesktop\Model
 * @method int getCompanyId()
 * @method string getCompanyName()
 * @method boolean getStatus()
 */
class Company extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_qbd_company';

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\QuickBooksDesktop\Model\ResourceModel\Company');
    }
}
