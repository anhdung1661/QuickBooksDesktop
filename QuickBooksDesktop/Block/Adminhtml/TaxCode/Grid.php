<?php

namespace Magenest\QuickBooksDesktop\Block\Adminhtml\TaxCode;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid as WidgetGrid;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Tax\Model\Calculation\Rate;

/**
 * Class Grid
 * @package Magenest\QuickBooksDesktop\Block\Adminhtml\TaxCode
 */
class Grid extends Extended
{

    protected $_taxRateCollection;

    /**
     * Grid constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param Rate $taxRateCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Rate $taxRateCollection,
        array $data = []
    ) {
        $this->_taxRateCollection = $taxRateCollection;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $backendHelper, $data);
        $this->setEmptyText(__('No Results Found'));
    }


    /**
     * @return Grid|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('tax_calculation_rate_id', [
            'header' => __('Tax Rate ID'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'tax_calculation_rate_id'
        ]);

        $this->addColumn('code', [
            'header' => __('Tax Title'),
            'align' => 'left',
            'index' => 'code'
        ]);

        $this->addColumn('rate', [
            'header' => __('Rate'),
            'align' => 'right',
            'index' => 'rate'
        ]);

        $this->addColumn('code_mapping', [
            'header' => __('Taxes in Quickbooks'),
            'index' => 'code_mapping',
            'align' => 'left',
            'filter'    => false,
            'sortable' => false,
            'renderer' => \Magenest\QuickBooksDesktop\Block\Adminhtml\TaxCode\Renderer\QuickbooksTaxes::class
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Initialize the Result collection
     *
     * @return WidgetGrid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_taxRateCollection->getCollection());
        return parent::_prepareCollection();
    }
}
