<?php


namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse\Queue\Customer;


interface CustomerInformation
{
    const XML_CUSTOMER_NAME = ['tag_name' => 'Name', 'value_length' => 41];

    const XML_CUSTOMER_COMPANY_NAME = ['tag_name' => 'CompanyName', 'value_length' => 41];

    const XML_CUSTOMER_FIRST_NAME = ['tag_name' => 'FirstName', 'value_length' => 25];

    const XML_CUSTOMER_LAST_NAME = ['tag_name' => 'LastName', 'value_length' => 25];

    /**
     * Billing address
     */
    const XML_BILL_ADDRESS_LINE_1 = ['tag_name' => ['Addr1'], 'value_length' => 41];

    const XML_BILL_ADDRESS_LINE_2 = ['tag_name' => ['Addr2'], 'value_length' => 41];

    const XML_BILL_ADDRESS_LINE_3 = ['tag_name' => ['Addr3'], 'value_length' => 41];

    const XML_BILL_ADDRESS_LINE_4 = ['tag_name' => ['Addr4'], 'value_length' => 41];

    const XML_BILL_ADDRESS_LINE_5 = ['tag_name' => ['Addr5'], 'value_length' => 41];

    const XML_BILL_ADDRESS_CITY = ['tag_name' => ['City'], 'value_length' => 31];

    const XML_BILL_ADDRESS_STATE = ['tag_name' => ['State'], 'value_length' => 21];

    const XML_BILL_ADDRESS_POSTAL_CODE = ['tag_name' => ['PostalCode'], 'value_length' => 13];

    const XML_BILL_ADDRESS_COUNTRY = ['tag_name' => ['Country'], 'value_length' => 31];

    const XML_BILL_ADDRESS_NOTE = ['tag_name' => ['Note'], 'value_length' => 41];

    /**
     * Shipping address
     */

    const XML_SHIP_ADDRESS_LINE_1 = ['tag_name' => ['Addr1'], 'value_length' => 41];

    const XML_SHIP_ADDRESS_LINE_2 = ['tag_name' => ['Addr2'], 'value_length' => 41];

    const XML_SHIP_ADDRESS_LINE_3 = ['tag_name' => ['Addr3'], 'value_length' => 41];

    const XML_SHIP_ADDRESS_LINE_4 = ['tag_name' => ['Addr4'], 'value_length' => 41];

    const XML_SHIP_ADDRESS_LINE_5 = ['tag_name' => ['Addr5'], 'value_length' => 41];

    const XML_SHIP_ADDRESS_CITY = ['tag_name' => ['City'], 'value_length' => 31];

    const XML_SHIP_ADDRESS_STATE = ['tag_name' => ['State'], 'value_length' => 21];

    const XML_SHIP_ADDRESS_POSTAL_CODE = ['tag_name' => ['PostalCode'], 'value_length' => 13];

    const XML_SHIP_ADDRESS_COUNTRY = ['tag_name' => ['Country'], 'value_length' => 31];

    const XML_SHIP_ADDRESS_NOTE = ['tag_name' => ['Note'], 'value_length' => 41];


    const XML_CUSTOMER_PHONE = ['tag_name' => 'Phone', 'value_length' => 21];

    const XML_CUSTOMER_EMAIL = ['tag_name' => 'Email', 'value_length' => 1023];
}