<?php

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceMod;

use Magenest\QuickBooksDesktop\Api\Data\QueueInterface;

/**
 * Class SendRequestXml
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceMod
 */
class SendRequestXml extends \Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Sales\InvoiceAdd\SendRequestXML
{
    /**
     * @inheritDoc
     */
    protected function getMagentoType()
    {
        return QueueInterface::TYPE_EDIT_INVOICE;
    }
}