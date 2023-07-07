<?php

namespace Magenest\QuickBooksDesktop\Helper;

/**
 * Class PaymentMethodMapping
 * @package Magenest\QuickBooksDesktop\Helper
 */
class PaymentMethodMapping
{
    const QUICK_BOOKS_METHOD = 'qbMethod';

    const QUICK_BOOKS_DEPOSIT_ACCOUNT = 'depositAccount';

    /**
     * Get payment method and deposit account.
     * @param $magentoPaymentMethod
     * @return array|string[]|boolean
     */
    public static function getPaymentMethodAndAccountMapping($magentoPaymentMethod)
    {
        $creditCart = [self::QUICK_BOOKS_METHOD => 'Credit Card', self::QUICK_BOOKS_DEPOSIT_ACCOUNT => 'PayPal Account'];
        $unDeposit = [self::QUICK_BOOKS_METHOD => false, self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12000 · Undeposited Funds'];
        $mapping = [
            'payflow_link' => $creditCart, //Credit Cart
            'braintree' => $creditCart, //Pay with Credit Card
            'braintree_paypal' => $creditCart,//Pay with PayPal
            'paypal_billing_agreement' => $creditCart,//PayPal Billing Agreement
            'braintree_ach_direct_debit' => $creditCart,
            'paypal_express' => $creditCart,//PayPal Checkout
            'braintree_paypal_credit' => $creditCart,//PayPal Credit
            'paypal_express_bml' => $creditCart,//PayPal Credit (Paypal Express Bml)
            'payflow_express_bml' => $creditCart,//PayPal Credit (Payflow Express Bml)
            'hosted_pro' => $creditCart,//Payment by cards or by PayPal account
            'braintree_paypal_vault' => $creditCart,//Stored Accounts (PayPal)
            'braintree_cc_vault' => $creditCart,//Stored Cards (Braintree)
            'payflowpro_cc_vault' => $creditCart,//Stored Cards (Payflow Pro)
            'payflow_express' => $creditCart,
            'payflowpro' => $creditCart,//Credit Card or Debit Card
            'payflow_advanced' => $creditCart,//Credit Card
            'paypal_direct' => $creditCart,//Credit Card
            'paypal_standard' => $creditCart,//PayPal Website Payments Standard
            'authorizenet_directpost' => $creditCart,//Credit Card Direct Post (Authorize.net)
            'authorizenet' => $creditCart,//Credit Card (Authorize.net)
            'm2epropayment' => [//M2E Pro Payment
                self::QUICK_BOOKS_METHOD => 'Amazon',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12300 · Amazon Payment'
            ],
            'cashondelivery1' => [//Zoro Payment
                self::QUICK_BOOKS_METHOD => 'Zoro',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => 'Zoro Payment'
            ],
            'zoropayment' => [//Zoro Payment
                self::QUICK_BOOKS_METHOD => 'Zoro',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => 'Zoro Payment'
            ],
            'cashondelivery2' => [//Ebay Payment
                self::QUICK_BOOKS_METHOD => 'Ebay',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => 'Ebay Payment'
            ],
            'cashondelivery3' => [//Walmart Payment
                self::QUICK_BOOKS_METHOD => 'Walmart',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => 'Walmart Payment'
            ],
            'cashondelivery4' => [//Apple Payment
                self::QUICK_BOOKS_METHOD => 'Apple',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12301 · Apple Pay',
            ],
            'braintree_applepay' => [//Apple Pay
                self::QUICK_BOOKS_METHOD => 'Apple',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12301 · Apple Pay',
            ],
            'cashondelivery5' => [//Google Payment
                self::QUICK_BOOKS_METHOD => 'Google',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12302 · Google Pay'
            ],
            'braintree_googlepay' => [//Google Pay
                self::QUICK_BOOKS_METHOD => 'Google',
                self::QUICK_BOOKS_DEPOSIT_ACCOUNT => '12302 · Google Pay'
            ],
            'checkmo' => $unDeposit,//Check / Money order
            'cashondelivery' => $unDeposit,//Cash On Delivery
            'free' => $unDeposit,//Purchase Order Payment
            'purchaseorder' => $unDeposit//Purchase Order
        ];

        if (array_key_exists($magentoPaymentMethod, $mapping)) {
            return $mapping[$magentoPaymentMethod];
        }

        return false;
    }
}
