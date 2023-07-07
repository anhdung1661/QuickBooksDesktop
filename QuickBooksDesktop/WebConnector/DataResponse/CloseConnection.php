<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\WebConnector\DataResponse;

/**
 * Class CloseConnection
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
class CloseConnection
{
    /**
     * Specify a string that you want the web connector to display to the user showing the status of the web service action on behalf of your user.
     * This string will be displayed in the web connector UI in the status column
     * @var string
     */
    public $closeConnectionResult;

    /**
     * CloseConnection constructor
     *
     * @param string $message
     */
    public function __construct($message = 'Complete!')
    {
        $this->closeConnectionResult = $message;
    }
}
