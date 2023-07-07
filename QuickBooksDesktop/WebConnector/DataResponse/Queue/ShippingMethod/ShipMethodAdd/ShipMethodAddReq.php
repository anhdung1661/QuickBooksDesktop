<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd;

/**
 * Interface ShipMethodAddReq
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\ShippingMethod\ShipMethodAdd
 */
interface ShipMethodAddReq
{
    const XML_SHIP_METHOD_ADD = 'ShipMethodAdd';

    const XML_SHIP_METHOD_NAME = ['tag_name' => 'Name', 'value_length' => 15];
}