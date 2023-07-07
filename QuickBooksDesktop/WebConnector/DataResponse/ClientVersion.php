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
 * return value after QWC send clientVersion()
 * A string telling the web connector what to do next
 * @package Magenest\QuickBooksDesktop\WebConnector\DataResponse
 */
class ClientVersion
{
    /**
     * Specify an empty string or Null if you want the web connector to proceed with the update
     */
    const PROCEED_UPDATE = '';

    /**
     * Specify a text string that begins with the characters "W:" if you want the web connector to display a WARNING dialog prompting the user to continue with the update or cancel it.
     * The text string after the “W:” will be displayed in the warning dialog.
     * example: "W:We recommend that you upgrade your QBWebConnector" will be displayed in the warning dialog
     */
    const PREFIX_DISPLAY_WARNING = 'W:';

    /**
     * Specify a text string that begins with the characters "E:" if you want the web connector to cancel the update and display an ERROR dialog.
     * The text string after the “E:” will be displayed in the error dialog.
     * The user will have to download a new version of the web connector to continue with the update
     * example: "E:You need to upgrade your QBWebConnector" will be displayed in the error dialog
     */
    const PREFIX_DISPLAY_ERROR = 'E:';

    /**
     * Supply a value of O: (O as in Okay, not zero, followed by the QBWC version supported by the web service).
     * For example O:2.0. This tells the user that the server expects a newer version of QBWC than the user currently has but also tells the user which version is needed.
     */
    const PREFIX_DISPLAY_EXPECT_VERSION = 'O:';

    /**
     * return to QWC one of constants
     * @var string
     */
    protected $clientVersionResult;

    /**
     * ClientVersion constructor.
     * @param string $version
     */
    public function __construct($string = self::PROCEED_UPDATE)
    {
        $this->clientVersionResult = $string;
    }
}