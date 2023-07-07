<?php

namespace Magenest\QuickBooksDesktop\Block\Adminhtml\TaxCode\Renderer;

use Magenest\QuickBooksDesktop\Api\Data\TaxesInterface;
use Magenest\QuickBooksDesktop\Api\Data\TaxesMappingInterface;
use Magenest\QuickBooksDesktop\Model\ResourceModel\Taxes\CollectionFactory as TaxesCollection;
use Magenest\QuickBooksDesktop\Model\ResourceModel\TaxesMapping\CollectionFactory as TaxesMappingCollection;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Code
 * @package Magenest\QuickBooksDesktop\Block\Adminhtml\TaxCode\Renderer
 */
class QuickbooksTaxes extends AbstractRenderer
{

    /**
     * @var TaxesCollection
     */
    protected $_taxes;

    /**
     * @var TaxesMappingCollection
     */
    protected $_taxesMapping;

    /**
     * Website constructor.
     * @param TaxesCollection $taxes
     * @param TaxesMappingCollection $taxesMappingCollection
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        TaxesCollection $taxes,
        TaxesMappingCollection $taxesMappingCollection,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_taxes = $taxes;
        $this->_taxesMapping = $taxesMappingCollection;
    }

    /**
     * Render the grid cell value
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $taxesQB = $this->_taxes->create()->getItems();

        $magentoTaxId = $row->getData('tax_calculation_rate_id'); // magento tax id
        $qbMappedTaxId = $this->_taxesMapping->create()->addFieldToFilter(TaxesMappingInterface::MAGENTO_ID, $magentoTaxId)->getLastItem()->getData(TaxesMappingInterface::QUICKBOOKS_ENTITY_ID);

        $hidden = '<input type="hidden"' . 'name="taxRow[' . $magentoTaxId . '][' .TaxesMappingInterface::MAGENTO_ID. ']"' . ' value="' . $magentoTaxId . '">';
        $input = '<select required name="taxRow[' . $magentoTaxId . '][' .TaxesMappingInterface::QUICKBOOKS_ENTITY_ID. ']"' . ' class=" select admin__control-select">';
        foreach ($taxesQB as $tax) {
            $selected = ($tax->getId() == $qbMappedTaxId) ? 'selected="selected"' : '';
            $input .= '<option title="' .$tax->getData(TaxesInterface::TAX_NOTE). ' ' .$tax->getData(TaxesInterface::TAX_VALUE). '" 
                               value="' . $tax->getId() . '" ' .$selected. '>' . $tax->getData(TaxesInterface::TAX_CODE) . '</option>';
        }
        $input .= '</select>';

        return $hidden . $input;
    }
}
