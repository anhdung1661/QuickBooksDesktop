<?php

namespace Magenest\QuickBooksDesktop\Plugin\Request;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

class CsrfValidator
{
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $object
     * @param callable $proceed
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @return bool
     */
    public function aroundValidate(
        \Magento\Framework\App\Request\CsrfValidator $object,
        callable $proceed,
        RequestInterface $request,
        ActionInterface $action
    ) {
        if (!preg_match('/qbdesktop\/connection/i', $request->getUri()->getPath())) {
            $proceed($request, $action);
        }

        return true;
    }
}
