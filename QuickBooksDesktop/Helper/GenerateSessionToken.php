<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_QuickBooksDesktop extension
 * NOTICE OF LICENSE
 */

namespace Magenest\QuickBooksDesktop\Helper;

use Magenest\QuickBooksDesktop\Model\SessionConnect;
use Magenest\QuickBooksDesktop\Model\SessionConnectFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * This class generate a session token for a connection
 *
 * @package Magenest\QuickBooksDesktop\Helper
 */
class GenerateSessionToken
{
    /**
     * @var SessionConnectFactory
     */
    protected $_sessionConnectFactory;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * Generate constructor.
     * @param SessionConnectFactory $sessionConnectFactory
     * @param DateTime $date
     */
    public function __construct(
        SessionConnectFactory $sessionConnectFactory,
        DateTime $date
    ) {
        $this->_sessionConnectFactory = $sessionConnectFactory;
        $this->_date = $date;
    }

    /**
     * Generate new session token
     *
     * @return string
     * @throws \Exception
     */
    public function generate()
    {
        try {
            $ticketCode = $this->generateCode();
        } catch (\Exception $exception) {
            throw new \Exception('Error while generate session token. ' . $exception->getMessage());
        }

        return $ticketCode;
    }

    /**
     * Generate code
     *
     * @return mixed
     */
    public function generateCode()
    {
        $gen_arr = [];
        $pattern = '[A2][N1][A1][N1]-[A1][N1][A2][N1]-[N2][A1]-[A1][N1][A1][N2]-[A1][N1][A2][N2]';

        preg_match_all("/\[[AN][.*\d]*\]/", $pattern, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $delegate = substr($match [0], 1, 1);
            $length = substr($match [0], 2, strlen($match [0]) - 3);
            $gen = '';
            if ($delegate == 'A') {
                $gen = $this->generateString($length);
            } elseif ($delegate == 'N') {
                $gen = $this->generateNum($length);
            }

            $gen_arr [] = $gen;
        }
        foreach ($gen_arr as $g) {
            $pattern = preg_replace('/\[[AN][.*\d]*\]/', $g, $pattern, 1);
        }

        return $pattern;
    }

    /**
     * Generate String
     *
     * @param $length
     * @return string
     */
    public function generateString($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $c [rand(0, 51)];
        }

        return $rand;
    }

    /**
     * Generate Number
     *
     * @param $length
     * @return string
     */
    public function generateNum($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "0123456789";
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $c [rand(0, 9)];
        }

        return $rand;
    }
}
