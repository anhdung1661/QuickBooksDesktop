<?php
namespace Magenest\QuickBooksDesktop\Model\Config\Backend;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Company as CompanyResourceModel;

class Company extends \Magento\Framework\App\Config\Value
{

    protected $_configValueFactory;
    private $companyResourceModel;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        CompanyResourceModel $company,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_configValueFactory = $configValueFactory;
        $this->companyResourceModel = $company;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Framework\App\Config\Value
     * @throws \Exception
     */
    public function afterSave()
    {
        $companyName = $this->getValue();

        try {
            $this->companyResourceModel->disableAllCompany();
            if (!empty($companyName)) {
                $this->companyResourceModel->saveCompany($companyName);
            }
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save change company.'));
        }

        return parent::afterSave();
    }
}