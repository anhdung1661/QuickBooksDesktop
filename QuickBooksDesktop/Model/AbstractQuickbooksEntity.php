<?php

namespace Magenest\QuickBooksDesktop\Model;

use Magenest\QuickBooksDesktop\Api\Data\CompanyInterface;
use Magenest\QuickBooksDesktop\Api\Data\QuickbooksEntityInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company\Collection as CompanyCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class AbstractQuickbooksEntity
 * @package Magenest\QuickBooksDesktop\Model
 */
abstract class AbstractQuickbooksEntity extends AbstractModel
{
    /**
     * @var CompanyCollectionFactory
     */
    protected $_companyCollection;

    /**
     * AbstractQuickbooksEntity constructor.
     * @param CompanyCollectionFactory $companyCollection
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        CompanyCollectionFactory $companyCollection,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_companyCollection = $companyCollection;
    }
}